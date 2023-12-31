<?php

namespace App\Controller;

use App\Repository\ClubLigaMatchRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClubLigaController extends AbstractController
{
    /**
     * @Route("/club_liga_new", name="clubliganew")
     */
    public function index(ClubLigaMatchRepository $clubLigaMatchRepository)
    {
        $matches = $clubLigaMatchRepository->findAll();

        return $this->render('club_liga_new/index.html.twig', [
            'stand'     => $this->loadStand(),
            'matches'   => $matches,
            'rankings'  => $this->calculateRankings($matches),
        ]);
    }

    private function loadStand() : string
    {
        return '8.1.2023';
    }

    private function calculateRankings($matches)
    {
        foreach ($matches as $match) {
            $rankings[] = [
                'rank' => '1',
                'name' => $match->getUser1()->getShortName(),
            ];
            return $rankings;
        }
    }
}
