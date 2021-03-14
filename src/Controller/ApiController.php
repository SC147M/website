<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/events", name="api_events")
     * @param ReservationRepository $reservationRepository
     * @param Request               $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function events(
        ReservationRepository $reservationRepository,
        Request $request
    ): JsonResponse
    {
        $start = $request->query->get('start');
        $start = new DateTime($start);
        $end = $request->query->get('end');
        $end = new DateTime($end);

        $reservations = $reservationRepository->findByRange($start, $end);

        $result = [];
        foreach ($reservations as $reservation) {
            $tables = [];
            foreach ($reservation->getTables() as $table) {
                $tables[] = $table->getName() . ' ';
            }

            $participants = [];
            foreach ($reservation->getParticipants() as $participant) {
                $participants[] = $participant->getFirstName();
            }

            switch ($reservation->getType()) {
                case Reservation::TYPE_TABLE:
                    $title = 'Tischreservierung';
                    break;
                case Reservation::TYPE_LEAGUE_1:
                    $title = 'Ligaspiel 1ste Mannschaft';
                    break;
                case Reservation::TYPE_LEAGUE_2:
                    $title = 'Ligaspiel 2te Mannschaft';
                    break;
                case Reservation::TYPE_TOURNEY:
                    $title = 'Turnier';
                    break;
                default:
                    $title = 'Sonstiges';
                    break;
            }

            $result[] = [
                'id'            => $reservation->getId(),
                'editable'      => false,
                'className'     => 'event_' . $reservation->getType(),
                'title'         => $title,
                'start'         => $reservation->getStart()->format('Y-m-d H:i:s'),
                'end'           => $reservation->getEnd()->format('Y-m-d H:i:s'),
                'extendedProps' => [
                    'user'         => $reservation->getUser()->getFirstName(),
                    'created_at'   => $reservation->getCreatedAt()->format('d.m.Y H:i'),
                    'participants' => $participants,
                    'comment'      => nl2br($reservation->getComment()),
                    'tables'       => $tables,
                ],
            ];

        }

        /**/

        return $this->json($result);
    }

    /**
     * @Route("/api/deploy", name="api_deploy")
     * @param Request               $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deploy(
        Request $request
    ): JsonResponse
    {
        die('Markus, ich erwarte jetzt schon ein paar Blumen von dir, dass jetzt alles doch geklappt hat :)');
    }
}
