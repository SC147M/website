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
            'standings' => $this->loadStandings($matches),
        ]);
    }

    private function loadStand() : string
    {
        return '7.1.2023';
    }

    private function loadStandings($matches)
    {
        foreach ($matches as $match) {
            $rankings[] = [
                'rank' => '1',
                'user' => $match->getUser(),
            ];
            return $rankings;
        }
    }
}
