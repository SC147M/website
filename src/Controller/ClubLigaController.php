<?php

namespace App\Controller;

use App\Entity\ClubLigaMatch;
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
        $standings = $this->getStandings($matches);

        return $this->render('club_liga_new/index.html.twig', [
            'stand'     => '5.1.2023',
            'matches'   => $matches,
            'standings' => $standings,
        ]);
    }

    private function getStandings(ClubLigaMatch[] $matches)
    {
        return [];
    }
}
