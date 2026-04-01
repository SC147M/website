<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\NewsRepository;
use App\Repository\ReservationRepository;
use App\Repository\TextContentRepository;
use App\Services\ReservationFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;


class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ReservationRepository $reservationRepository, NewsRepository $newsRepository, ReservationFormatter $reservationFormatter, TextContentRepository $textContentRepository): Response
    {
        $today = new DateTime(date("Y-m-d H:i:s"));
        $reservationsHorizon = new DateTime("+ 2 week"); /* now two weeks, Buchungen der nächsten 2 Wochen werden angezeigt */
        $tournamentsHorizon = new DateTime("+ 16 week"); /*tournaments/leagues: 16 weeks */
        $tourneys = $reservationRepository->findByTypes([Reservation::TYPE_TOURNEY, Reservation::TYPE_LEAGUE_1, Reservation::TYPE_LEAGUE_2, Reservation::TYPE_LEAGUE_3], $today, $tournamentsHorizon);
        $reservations = $reservationRepository->findByTypes([Reservation::TYPE_TABLE, Reservation::TYPE_OTHERS], $today, $reservationsHorizon);
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
}