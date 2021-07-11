<?php

namespace App\Form;

use App\Entity\SnookerBreak;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnookerBreakType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var SnookerBreak $data */
        $data = $options['data'];

        $builder
            ->add('score')
            ->add('opponent',
                EntityType::class,
                [
                    'query_builder' => function (EntityRepository $er) use ($data) {
                        return $er->createQueryBuilder('u')
                            ->where('u.id != ' . $data->getUser()->getId())
                            ->orderBy('u.firstName')
                            ->addOrderBy('u.lastName');
                    },
                    'multiple'      => false,
                    'class'         => User::class,
                    'required'      => true,
                    'placeholder'   => 'Bitte einen Gegner auswählen',
                ]);
//            ->add('created_at',
//                DateTimeType::class,
//                [
//                    'widget' => 'single_text',
//                    'html5'  => false,
//                    'label'  => false,
//                    'attr'   => [
//                        'class' => 'js-datepicker',
//                    ],
//
//                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SnookerBreak::class,
        ]);
    }
}
