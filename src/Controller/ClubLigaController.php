<?php

namespace App\Controller;

use App\Entity\ClubLigaMatch;
use App\Entity\User;
use App\Repository\ClubLigaMatchRepository;
use App\Repository\RankingRepository;
use App\Repository\UserRepository;
use App\Services\ClubLiga\ClubLigaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClubLigaController extends AbstractController
{
    /**
     * @Route("/clubligadb", name="clubligadb")
     * @param ClubLigaService          $clubLigaService
     * @param RankingRepository        $rankingRepository
     * @param ClubLigaMatchRepository  $matchRepository
     * @return Response
     */
    public function index(Request $request, ClubLigaService $clubLigaService, RankingRepository $rankingRepository, ClubLigaMatchRepository $matchRepository)
    {
        $message = $request->get('message', '');

        /** @var User $user */
        $user = $this->getUser();
        $opponentsToChallenge = null;

        $rankings = null;

        $clubLiga = [];

        $lastMatches = $matchRepository->findBy(['state' => ClubLigaMatch::STATE_DONE], ['updated' => 'DESC'], 10);

        return $this->render('clubLiga/db.html.twig', [
            'clubLiga'             => $clubLiga,
            'player'               => $player,
            'opponentsToChallenge' => $opponentsToChallenge,
            'lastMatches'          => $lastMatches,
            'user'                 => $user,
            'message'              => $message,
        ]);
    }

    /**
     * @Route("/clubLiga/directresult", name="clubLiga_report_direct_result")
     * @param Request        $request
     * @param ClubLigaService $clubLigaService
     * @param UserRepository $userRepository
     * @return Response
     */
    public function reportDirectResult(Request $request, ClubLigaService $clubLigaService, UserRepository $userRepository)
    {
        $opponentId = $request->get('opponent_id');
        $opponent = $userRepository->findOneBy(['id' => $opponentId]);

        $score1 = (int)$request->get('score1');
        $score2 = (int)$request->get('score2');

        if ($score1 === $score2) {
            return $this->render('clubLiga/report.html.twig', ['type' => 1]);
        }

        if ($score1 < 2 && $score2 < 2) {
            return $this->render('clubLiga/report.html.twig', [
                'type'   => 2,
                'score1' => $score1,
                'score2' => $score2,
            ]);
        }

        /** @var User $user */
        $user = $this->getUser();

        $clubLigaService->reportMatch($user, $opponent, $score1, $score2);

        return $this->redirectToRoute('clubLiga', ['message' => 'success']);
    }

    /**
     * @Route("/clubLiga/recalc", name="clubLiga_recalc")
     * @param Request                $request
     * @param RankingRepository      $repository
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function recalc(Request $request, RankingRepository $repository, EntityManagerInterface $manager)
    {
        $ranking = $repository->findBy([], ['position' => 'asc']);
        $position = 1;

        foreach ($ranking as $rank) {
            $rank->setPosition($position);
            $position++;

            $manager->persist($rank);
        }

        $manager->flush();

        return $this->redirectToRoute('easyadmin', [
            'entity'       => 'Ranking',
            'action'       => 'list',
            'menuIndex'    => '8',
            'submenuIndex' => '0',
        ]);

    }

    /**
     * @var int $rank
     * @return float|int
     */
    private function getRow(int $rank): int
    {
        return round((-1 + sqrt(8 * $rank) / 2)) + 1;
    }

    private function getMaxChallengeRank(int $rank)
    {
        /*
         *     1
         *    2 3
         *   4 5 6
         *  7 8 9 10
         */
        $row = $this->getRow($rank);

        if ($rank <= 3) {
            return 1;
        }

        return $rank + 1 - $row;
    }
}
