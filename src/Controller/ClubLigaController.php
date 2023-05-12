<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClubLigaController extends AbstractController
{
    /**
     * @Route("/club_liga_new", name="clubliganew")
     */
    public function index()
    {
        return $this->render('club_liga_new/index.html.twig', ['stand' => '1.1.2023']);
    }
}
