<?php

namespace App\Controller;

use App\Entity\Match;
use App\Entity\User;
use App\Repository\MatchRepository;
use App\Repository\RankingRepository;
use App\Repository\UserRepository;
use App\Services\Pyramid\PyramidService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PyramidController extends AbstractController
{
    /**
     * @Route("/pyramid", name="pyramid")
     * @param PyramidService    $pyramidService
     * @param RankingRepository $rankingRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PyramidService $pyramidService, RankingRepository $rankingRepository, MatchRepository $matchRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $opponentToChallenge = $pyramidService->getValidOpponents($user);

        $ranking = $user->getRanking();
        if ($ranking) {
            $rank = $user->getRanking()->getPosition();
            $player = [
                'id'  => $user->getId(),
                'min' => $rank - 1,
                'max' => $this->getMaxChallengeRank($rank),
            ];
        } else {
            $player = [
                'id'  => $user->getId(),
                'min' => $rankingRepository->findOneBy([], ['position' => 'DESC'])->getPosition(),
                'max' => 1,
            ];
        }

        $rankings = $rankingRepository->findBy([], ['position' => 'ASC']);

        $row = 1;
        $challengers = [];
        foreach ($rankings as $ranking) {

            $row = $this->getRow($ranking->getPosition());

            $challengers[] = [
                'id'    => $ranking->getUser()->getId(),
                'valid' => true,
                'rank'  => $ranking->getPosition(),
                'row'   => $row,
                'user'  => $ranking->getUser(),
            ];

        }

        $pyramid = [];
        foreach ($challengers as $challenger) {
            $pyramid[$challenger['row']][] = $challenger;
        }

        $gap = $row - count($pyramid[$row]);
        if ($gap > 0) {
            for ($i = 0; $i < $gap; $i++) {
                $pyramid[$row][] = [
                    'id'    => null,
                    'valid' => false,
                ];
            }
        }

        $lastMatches = $matchRepository->findBy(['state' => Match::STATE_DONE], ['updated' => 'DESC'], 10);

        return $this->render('pyramid/index.html.twig', [
            'pyramid'             => $pyramid,
            'player'              => $player,
            'opponentToChallenge' => $opponentToChallenge,
            'lastMatches'         => $lastMatches,
        ]);
    }


    /**
     * @Route("/pyramid/directresult", name="pyramid_report_direct_result")
     * @param Request        $request
     * @param PyramidService $pyramidService
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function reportDirectResult(Request $request, PyramidService $pyramidService, UserRepository $userRepository)
    {
        $opponentId = $request->get('opponent_id');
        $opponent = $userRepository->findOneBy(['id' => $opponentId]);

        /** @var User $user */
        $user = $this->getUser();

        $pyramidService->reportMatch($user, $opponent, 0);

        return $this->redirectToRoute('pyramid');
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

        if ($rank <= 4) {
            return 1;
        }

        return $rank + 1 - $row;
    }
}
