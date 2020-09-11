<?php

namespace App\Validator;

use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Reservation extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';
    /**
     * @var ReservationRepository
     */
    public $repository;

    /**
     * Reservation constructor.
     * @param null                  $options
     * @param ReservationRepository $repository
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }
}
