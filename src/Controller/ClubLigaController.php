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
        try {
            // $matches = $clubLigaMatchRepository->findBy(['state' => ClubLigaMatch::STATE_DONE], ['updated' => 'DESC'], 10);

            return $this->render('club_liga_new/index.html.twig', [
                'stand' => '3.1.2023',
                'matches' => [],
            ]);
        }
        catch(Exception $ex) {
            return $this->render('club_liga_new/index.html.twig', [
                'stand' => $ex->getMessage(),
                'matches' => [],
            ]);
        }
    }
}
