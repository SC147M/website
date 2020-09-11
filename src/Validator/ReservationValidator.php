<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReservationValidator extends ConstraintValidator
{
    /**
     * @param mixed                  $value
     * @param Constraint|Reservation $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        var_dump($value); die();

        /* @var $constraint \App\Validator\Reservation */
        $constraint->repository->findByRange();


        if (null === $value || '' === $value) {
            //        return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
