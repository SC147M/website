<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClubController
 * @package App\Controller
 */
class ClubController extends AbstractController
{
    /**
     * @Route("/club", name="club")
     */
    public function club()
    {
        return $this->render('club/club.html.twig');
    }

    /**
     * @Route("/club/spielbetrieb", name="fixtures")
     * @param ReservationRepository $reservationRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function fixtures(ReservationRepository $reservationRepository)
    {
        $today = (new DateTime())->setTime(0, 0, 0, 0);

        $leagueOneGames = $reservationRepository->findByTypes(Reservation::TYPE_LEAGUE_1, $today);
        $leagueTwoGames = $reservationRepository->findByTypes(Reservation::TYPE_LEAGUE_2, $today);

        return $this->render('club/fixtures.html.twig', [
            'league_1_games' => $leagueOneGames,
            'league_2_games' => $leagueTwoGames,
        ]);
    }

    protected function loadProviderListings($data): void
    {
        if ($this->providerFiles !== null) {
            parent::loadProviderListings($data);

            return;
        }

        $data = [$data];

        while ($data) {
            $this->providerFiles = [];

            foreach ($data as $d) {
                $this->loadProviderListings($d);
            }

            $loadingFiles = $this->providerFiles;
            $this->providerFiles = null;

            $data = [];

            $this->rfs->download($loadingFiles, function (...$args) use (&$data): void {
                $data[] = $this->fetchFile(...$args);
            });
        }
    }

}
