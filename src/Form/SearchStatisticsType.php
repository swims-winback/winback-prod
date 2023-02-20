<?php

namespace App\Form;

use App\Entity\Statistic;
use App\Entity\Statistics\StatisticAccessoires;
use App\Entity\Statistics\StatisticPatho;
use App\Entity\Statistics\StatisticPathoType;
use App\Entity\Statistics\StatisticSn;
use App\Entity\Statistics\StatisticZone;
use App\Repository\StatisticRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchStatisticsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accessoires', EntityType::class, [
                'class' => StatisticAccessoires::class,
                'choice_label' => 'accessoires',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('patho', EntityType::class, [
                'class' => StatisticPatho::class,
                'choice_label' => 'patho',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('type_patho', EntityType::class, [
                'class' => StatisticPathoType::class,
                'choice_label' => 'type_patho',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('SN', EntityType::class, [
                'class' => StatisticSn::class,
                'choice_label' => 'SN',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('zone', EntityType::class, [
                'class' => StatisticZone::class,
                'choice_label' => 'zone',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-orange btn-outline-orange'
                ]
            ])
        ;
    }
}