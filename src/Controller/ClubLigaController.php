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
        ]);
    }

    private function loadStand()
    {
        return '6.1.2023';
    }
}
