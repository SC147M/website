<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\NewsRepository;
use App\Repository\ReservationRepository;
use App\Repository\TextContentRepository;
use App\Services\ReservationFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ReservationRepository $reservationRepository
     * @param NewsRepository        $newsRepository
     * @param ReservationFormatter  $reservationFormatter
     * @param TextContentRepository $textContentRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ReservationRepository $reservationRepository, NewsRepository $newsRepository, ReservationFormatter $reservationFormatter, TextContentRepository $textContentRepository)
    {
        $today = new DateTime(date("Y-m-d H:i:s"));
        $tourneys = $reservationRepository->findByTypes([Reservation::TYPE_TOURNEY, Reservation::TYPE_LEAGUE_1, Reservation::TYPE_LEAGUE_2, Reservation::TYPE_LEAGUE_3], $today);
        $reservations = $reservationRepository->findByTypes([Reservation::TYPE_TABLE, Reservation::TYPE_LEAGUE_3, Reservation::TYPE_OTHERS], $today);
        $reservations = $reservationFormatter->formatByDays($reservations);

        $news = $newsRepository->findOneBy([], ['createdAt' => 'desc']);


        $homeText = $textContentRepository->find(1);


        return $this->render(
            'index/index.html.twig',
            [
                'news'         => $news,
                'reservations' => $reservations,
                'tourneys'     => $tourneys,
                'homeText'     => $homeText,
            ]
        );
    }

    /**
     * @Route("/clubliga", name="clubliga")
     */
    public function clubLiga() {
        return $this->render('club_liga/index.html.twig');
    }

    /**
     * @Route("/timer", name="timer")
     */
    public function shootoutTimer() {
        return $this->render('index/shootouttimer.html.twig');
    }

    /**
     * @Route("/mitgliedschaft", name="membership")
     */
    public function membership()
    {
        return $this->render('index/costs.html.twig');
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
        return $this->render('index/faq.html.twig');
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
     * @Route("/games", name="games")
     */
    public function games()
    {
        return $this->render('index/games.html.twig');
    }

    /**
     * @Route("/training", name="training")
     */
    public function training()
    {
        return $this->render('index/training.html.twig');
    }

    /**
     * @Route("/teams", name="teams")
     */
    public function teams()
    {
        return $this->render('index/teams.html.twig');
    }

    /**
     * @Route("/breaks_old", name="breaks_old")
     */
    public function breaks()
    {
        return $this->render('index/breaks.html.twig');
    }
}