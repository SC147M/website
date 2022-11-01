<?php
/**
 * Created by PhpStorm.
 * User: christianlorenz
 * Date: 16.05.21
 * Time: 11:31
 */

namespace App\Services;

use App\Entity\Reservation;
use DateInterval;
use \DateTime;

class ReservationFormatter
{
    /**
     * @param Reservation[] $reservations
     * @return array
     */
    public function formatByDays($reservations)
    {
        $days = [];
        foreach ($reservations as $reservation) {
            $key = $reservation->getStart()->format('d.m.Y');
            if (!isset($days[$key])) {
                $days[$key] = [
                    'date' => $this->formatDate($reservation->getStart()),
                    'reservations' => []
                ];

            }

            $days[$key]['reservations'][ ] = $reservation;
        }

        return $days;
    }

    private function formatDate(\DateTimeInterface $date)
    {
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        $testDate = clone $date;
        $testDate->setTime(0, 0, 0);

        $diff = $today->diff( $testDate );
        $diffDays = (integer)$diff->format( "%R%a" );

        switch( $diffDays ) {
            case 0:
                return 'Heute';
            case 1:
                return 'Morgen';
            default:
                return $date->format('d.m.Y');
        }
    }
}
