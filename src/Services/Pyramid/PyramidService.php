<?php
/**
 * Created by PhpStorm.
 * User: christianlorenz
 * Date: 30.08.20
 * Time: 10:41
 */

namespace App\Services\Pyramid;


use App\Entity\Match;
use App\Entity\Ranking;
use App\Entity\User;
use App\Repository\MatchRepository;
use App\Repository\RankingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;

class PyramidService
{
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
     * PyramidService constructor.
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

    public function getValidOpponents(User $user): array
    {
        $opponents = [];
        $ranking = $user->getRanking();
        if ($ranking === null) {
            $users = $this->userRepository->findAll();
            foreach ($users as $opponent) {
                if ($this->canPlay($opponent, $user)) {
                    $opponents[] = $opponent;
                }
            }
        } else {
            $opponents = $this->getOpponentsByRanking($user);
        }

       //
        return $opponents;
    }

    private function getOpponentsByRanking(User $user)
    {
        $minRank = $this->getMinChallengeRank($user->getRanking()->getPosition());
        $maxRank = $this->getMaxChallengeRank($user->getRanking()->getPosition());

        $rankings = $this->rankingRepository->findByInRange($minRank, $maxRank);
        $unranked = $this->userRepository->findUnranked();

        $opponents = [];

        foreach ($rankings as $ranking) {
            if ($this->canPlay($ranking->getUser(), $user)) {
                $opponents[] = $ranking->getUser();
            }
        }

        foreach ($unranked as $item) {
            if ($this->canPlay($item, $user)) {
                $opponents[] = $item;
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
     * @param int $rank
     * @return float|int
     */
    private function getMaxChallengeRank(int $rank): int
    {
        /*
         *     1
         *    2 3
         *   4 5 6
         *  7 8 9 10
         */
        $row = $this->getRow($rank);

        if ($rank == 1) {
            return 4;
        }

        return $rank + $row;
    }


    public function reportMatch(User $user, User $opponent, int $result)
    {
        $userRanking = $user->getRanking();
        $opponentRanking = $opponent->getRanking();

        if ($userRanking === null) {
            if ($opponentRanking === null) {
                $match = new Match();
                $match
                    ->setUser1($user)
                    ->setUser2($opponent)
                    ->setCreated(new \DateTime())
                    ->setUpdated(new \DateTime())
                    ->setState(Match::STATE_DONE)
                    ->setResult($result);

                $this->manager->persist($match);

                $lastRank = $this->rankingRepository->findOneBy([], ['position' => 'DESC']);
                $lastRankingPosition = 1;
                if ($lastRank !== null) {
                    $lastRankingPosition = $lastRank->getPosition();
                }


                if ($result === 0) {
                    $userNewPosition = $lastRankingPosition + 1;
                    $opponentNewPosition = $lastRankingPosition;
                } else {
                    $userNewPosition = $lastRankingPosition;
                    $opponentNewPosition = $lastRankingPosition + 1;
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

                $this->manager->flush();

                die();

            }
        }
    }


    private function canPlay(User $user, User $challenger): bool
    {
        $roles = $this->roleHierarchy->getReachableRoleNames($user->getRoles());

        if ($user->getId() === $challenger->getId()) {
            return false;
        }


        return in_array('ROLE_USER', $roles);
    }
}