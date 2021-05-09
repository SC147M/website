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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PyramidController extends AbstractController
{
    /**
     * @Route("/pyramid", name="pyramid")
     * @param PyramidService    $pyramidService
     * @param RankingRepository $rankingRepository
     * @param MatchRepository   $matchRepository
     * @return Response
     */
    public function index(Request $request, PyramidService $pyramidService, RankingRepository $rankingRepository, MatchRepository $matchRepository)
    {
        $message = $request->get('message', '');

        /** @var User $user */
        $user = $this->getUser();
        $opponentsToChallenge = $pyramidService->getValidOpponents($user);

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
                'max' => $opponentsToChallenge[0]->getRanking()->getPosition(),
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

        // Fill the last places in the last row for pyramid design
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
            'pyramid'              => $pyramid,
            'player'               => $player,
            'opponentsToChallenge' => $opponentsToChallenge,
            'lastMatches'          => $lastMatches,
            'user'                 => $user,
            'message'              => $message,
        ]);
    }


    /**
     * @Route("/pyramid/directresult", name="pyramid_report_direct_result")
     * @param Request        $request
     * @param PyramidService $pyramidService
     * @param UserRepository $userRepository
     * @return Response
     */
    public function reportDirectResult(Request $request, PyramidService $pyramidService, UserRepository $userRepository)
    {
        $opponentId = $request->get('opponent_id');
        $opponent = $userRepository->findOneBy(['id' => $opponentId]);

        $score1 = (int)$request->get('score1');
        $score2 = (int)$request->get('score2');

        if ($score1 === $score2) {
            return $this->render('pyramid/report.html.twig', ['type' => 1]);
        }

        if ($score1 < 2 && $score2 < 2) {
            return $this->render('pyramid/report.html.twig', [
                'type'   => 2,
                'score1' => $score1,
                'score2' => $score2,
            ]);
        }

        /** @var User $user */
        $user = $this->getUser();

        $pyramidService->reportMatch($user, $opponent, $score1, $score2);

        return $this->redirectToRoute('pyramid', ['message' => 'success']);
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
