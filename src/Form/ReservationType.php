<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Table;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'start',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'html5'  => false,
                    'label'  => false,
                    'attr'   => [
                        'class' => 'js-datepicker',
                    ],

                ]
            )
            ->add(
                'end',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'html5'  => false,
                    'attr'   => [
                        'class' => 'js-datepicker',
                    ],
                    'label'  => false,
                ]
            )
            ->add(
                'tables',
                EntityType::class,
                [
                    'multiple'    => true,
                    'class'       => Table::class,
                    'required'    => true,
                    'placeholder' => 'Bitte einen Tisch auswählen',
                ]
            )
            ->add('participants',
                EntityType::class,
                [
                    'multiple'    => true,
                    'class'       => User::class,
                    'required'    => true,
                    'placeholder' => 'Bitte einen Teilnehmer auswählen',
                ])
            ->add('comment');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Reservation::class,
            ]
        );
    }
}
