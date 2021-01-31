<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\NewsRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ReservationRepository $reservationRepository
     * @param NewsRepository        $newsRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(ReservationRepository $reservationRepository, NewsRepository $newsRepository)
    {
        $today = (new DateTime())->setTime(0, 0, 0, 0);

        $tourneys = $reservationRepository->findByTypes(Reservation::TYPE_TOURNEY, $today);
        $reservations = $reservationRepository->findByTypes([Reservation::TYPE_TABLE, Reservation::TYPE_OTHERS], $today);

        $news = $newsRepository->findOneBy([], ['createdAt' => 'desc']);

        return $this->render(
            'index/index.html.twig',
            [
                'news'         => $news,
                'reservations' => $reservations,
                'tourneys'     => $tourneys,
            ]
        );
    }

    /**
     * @Route("/mitgliedschaft", name="membership")
     */
    public function membership()
    {
        return $this->render('index/membership.html.twig');
    }

    /**
     * @Route("/anfahrt", name="map")
     */
    public function map()
    {
        return $this->render('index/map.html.twig');
    }

    /**
     * @Route("/service", name="service")
     */
    public function service()
    {
        return $this->render('index/service.html.twig');
    }

    /**
     * @Route("/impressum", name="imprint")
     */
    public function imprint()
    {
        return $this->render('index/imprint.html.twig');
    }

    /**
     * @Route("/datenschutz", name="dsgvo")
     */
    public function dsgvo()
    {
        return $this->render('index/dsgvo.html.twig');
    }

    /**
     * @Route("/teams", name="teams")
     */
    public function teams()
    {
        return $this->render('index/teams.html.twig');
    }
}
