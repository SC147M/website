<?php
/**
 * Created by copy and paste.
 * User: markuskruas
 * Date: 04.05.23
 * Time: 23:08
 */

namespace App\Services\ClubLeagueService;


use App\Entity\Match;
use App\Entity\Ranking;
use App\Entity\User;
use App\Repository\MatchRepository;
use App\Repository\RankingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;

class ClubLeagueService
{
    const RESULT_LOSS = 0;
    const RESULT_WIN = 1;

    /**
     * @var MatchRepository
     */
    private $matchRepository;
    /**
     * @var RankingRepository
     */
    private $rankingRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * ClubLeagueService constructor.
     * @param RoleHierarchyInterface $roleHierarchy
     * @param RankingRepository      $rankingRepository
     * @param MatchRepository        $matchRepository
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        RoleHierarchyInterface $roleHierarchy,
        RankingRepository $rankingRepository,
        MatchRepository $matchRepository,
        UserRepository $userRepository,
        EntityManagerInterface $manager
    )
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->rankingRepository = $rankingRepository;
        $this->matchRepository = $matchRepository;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    /**
     * @param User $user
     * @return array|User[]
     */
    public function getValidOpponents(User $user): array
    {
        $ranking = $user->getRanking();

        if ($ranking === null) {
            $opponents = $this->getOpponentsWhenUnranked($user);
        } else {
            $opponents = $this->getOpponentsByRanking($user);
        }

        return $opponents;
    }

    /**
     * @param User $user
     * @return array|User[]
     */
    private function getOpponentsWhenUnranked(User $user): array
    {
        $opponents = [];
        $lastPosition = $this->rankingRepository->findOneBy([], ['position' => 'desc'])->getPosition();
        $lastRow = $this->getRow($lastPosition);

        $users = $this->userRepository->findAll();
        foreach ($users as $opponent) {
            if ($this->canPlay($opponent, $user, $lastRow)) {
                $opponents[] = $opponent;
            }
        }

        usort($opponents, function (User $a, User $b) {
            if ($a->getRanking() && $b->getRanking()) {
                return ($a->getRanking()->getPosition() < $b->getRanking()->getPosition()) ? -1 : 1;
            } elseif ($a->getRanking() && $b->getRanking() === null) {
                return -1;
            } elseif ($a->getRanking() === null && $b->getRanking()) {
                return 1;
            } else {
                return (strtolower($a->getShortName()) > strtolower($b->getShortName())) ? 1 : -1;
            }
        });

        return $opponents;
    }

    /**
     * @param User $user
     * @return array|User[]
     */
    private function getOpponentsByRanking(User $user): array
    {
        $minRank = $this->getMinChallengeRank($user->getRanking()->getPosition());
        $maxRank = $user->getRanking()->getPosition();

        $rankings = $this->rankingRepository->findByInRange($minRank, $maxRank);

        $opponents = [];

        foreach ($rankings as $ranking) {
            if ($this->canPlay($ranking->getUser(), $user)) {
                $opponents[] = $ranking->getUser();
            }
        }

        return $opponents;
    }

    /**
     * @param int $rank
     * @return float|int
     */
    private function getRow(int $rank): int
    {
        return round((-1 + sqrt(8 * $rank) / 2)) + 1;
    }

    /**
     * @param int $rank
     * @return float|int
     */
    private function getMinChallengeRank(int $rank): int
    {
        /*
         *     1
         *    2 3
         *   4 5 6
         *  7 8 9 10
         */
        $row = $this->getRow($rank);

        if ($rank <= 4) {
            return 1;
        }

        return $rank + 1 - $row;
    }

    /**
     * @param User $user
     * @param User $opponent
     * @param int  $score1
     * @param int  $score2
     */
    public function reportMatch(User $user, User $opponent, int $score1, int $score2)
    {
        $result = ($score1 > $score2) ? self::RESULT_WIN : self::RESULT_LOSS;

        $userRanking = $user->getRanking();
        $opponentRanking = $opponent->getRanking();

        $match = new Match();
        $match
            ->setUser1($user)
            ->setUser2($opponent)
            ->setCreated(new \DateTime())
            ->setUpdated(new \DateTime())
            ->setState(Match::STATE_DONE)
            ->setResult($result)
            ->setScore1($score1)
            ->setScore2($score2);

        $this->manager->persist($match);

        // user unranked
        if ($userRanking === null) {

            // opponent also unranked
            if ($opponentRanking === null) {
                $lastRank = $this->rankingRepository->findOneBy([], ['position' => 'DESC']);

                // empty pyramid
                $lastRankingPosition = 1;
                if ($lastRank !== null) {
                    $lastRankingPosition = $lastRank->getPosition();
                }


                if ($result === self::RESULT_LOSS) {
                    $userNewPosition = $lastRankingPosition + 2;
                    $opponentNewPosition = $lastRankingPosition + 1;
                } else {
                    $userNewPosition = $lastRankingPosition + 1;
                    $opponentNewPosition = $lastRankingPosition + 2;
                }

                $rankUser = new Ranking();
                $rankUser
                    ->setUser($user)
                    ->setPosition($userNewPosition);
                $this->manager->persist($rankUser);

                $rankOpponent = new Ranking();
                $rankOpponent
                    ->setUser($opponent)
                    ->setPosition($opponentNewPosition);
                $this->manager->persist($rankOpponent);

                // opponent ranked
            } else {
                if ($result === self::RESULT_LOSS) {
                    $lastRank = $this->rankingRepository->findOneBy([], ['position' => 'DESC']);
                    $lastPosition = $lastRank->getPosition();
                    $lastPosition++;
                    $userRankingPosition = $lastPosition;
                } else {
                    $userRankingPosition = $opponent->getRanking()->getPosition();
                    $this->rankingRepository->increasePositions($userRankingPosition);
                }

                $rankUser = new Ranking();
                $rankUser
                    ->setUser($user)
                    ->setPosition($userRankingPosition);
                $this->manager->persist($rankUser);
            }


            // both ranked
        } else {
            $rankingOpponent = $opponent->getRanking();
            $userRanking = $user->getRanking();

            if ($result === self::RESULT_WIN && $userRanking->getPosition() > $rankingOpponent->getPosition()) {
                $this->rankingRepository->increasePositionsFromTo($rankingOpponent->getPosition(), $userRanking->getPosition());
                $userRanking->setPosition($rankingOpponent->getPosition());
                $this->manager->persist($userRanking);
            }
        }

        $this->manager->flush();
    }


    /**
     * @param User     $user
     * @param User     $challenger
     * @param int|null $lastRow
     * @return bool
     */
    private function canPlay(User $challenger, User $user, int $lastRow = null): bool
    {
        $challengerRoles = $this->roleHierarchy->getReachableRoleNames($challenger->getRoles());


        if ($user->getId() === $challenger->getId()) {
            return false;
        }

        if ($user->getRanking() === null) {
            if ($challenger->getRanking() === null) {
                return in_array('ROLE_USER', $challengerRoles);
            }

            $position = $challenger->getRanking()->getPosition();
            $row = $this->getRow($position);

            if ($row === $lastRow) {
                return in_array('ROLE_USER', $challengerRoles);
            }

            return false;
        }


        return in_array('ROLE_USER', $challengerRoles);
    }
}