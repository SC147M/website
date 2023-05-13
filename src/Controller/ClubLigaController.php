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

    private function loadStandings(array $matches)
    {
        $ranking = new Ranking();

        foreach ($matches as $match) {
            $ranking->setPosition(1);
            $ranking->setUser($match->getUser());
            break;
        }
        return array(1 => $ranking);
    }
}
