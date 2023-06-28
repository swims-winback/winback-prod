<?php

namespace App\Form;

use App\Entity\DeviceFamily;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDeviceType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('value', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter device serial number',
                ],
                'required' => false,
            ])
            
            ->add('category', EntityType::class, [
                'class' => DeviceFamily::class,
                'label' => 'Device Type :',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            
            ->add('version', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter device version number',
                ],
                'required' => false,
            ])
            
            /*
            ->add('version', EntityType::class, [
                'class' => Software::class,
                'label' => 'Device Version :',
                'choice_label' => 'version',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            */
            
            ->add('versionUpload', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter device version upload number',
                ],
                'required' => false,
            ])
            
            /*
            ->add('versionUpload', EntityType::class, [
                'class' => Software::class,
                'label' => 'Device Version Upload :',
                'choice_label' => 'version',
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
            ])
            */
            ->add('forced', ChoiceType::class, [
                'label' => 'Device Forced :',
                'choices' => [
                    'Forced' => true,
                    'Not forced' => false,
                ],
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'required' => false,
            ])
            
            ->add('max_result', ChoiceType::class, [
                'label' => 'Max result: ',
                'choices'  => [
                    '10' => 10,
                    '25' => 25,
                    '50' => 50,
                ],
                'attr' => [
                    'class' => 'w-25',
                ],
                'required' => false,
            ])
            
            /*
            ->add('connected', ChoiceType::class, [
                'label' => 'Device Connected :',
                'choices' => [
                    'Connected' => true,
                    'Not connected' => false,
                ],
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'required' => false,
            ])
            */
            ->add('Search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn bg-orange btn-outline-orange',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Device::class,
        ]);
    }
}
