<?php

namespace App\Form;

use App\Class\SearchSn;
use App\Entity\DeviceFamily;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('q', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Search',
                ],
                'required' => false,
            ])
            /*
            ->add('date', RangeType::class, [
                'attr' => [
                    'min' => '2014-01-08',
                    'max' => '2018-01-08'
                ],
            ])
            
            ->add('min', DateType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date min'
                ]
            ])
            ->add('max', DateType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Date max'
                ]
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchSn::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
