<?php

namespace App\Form;

use App\Entity\DeviceFamily;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevicePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
            ->add('value', ButtonType::class, [
                'label' => 2,
                //'value' => 2,
                'attr' => [
                    'class' => 'form-control',
                    'value' => 2,
                    //'placeholder' => 'Enter page number',
                ],
            ])
            ;
            */
            ->add('value', TextType::class, [
                'label' => false,
                //'value' => 2,
                'attr' => [
                    'class' => 'form-control',
                    'value' => 2,
                    'disabled' => 'disabled',
                    //'placeholder' => 'Enter page number',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'text-center btn fa-regular fa-chevrons-left text-dark',
                ],
                //'value' => 2,
                /*
                'attr' => [
                    'class' => 'form-control',
                    'value' => 2,
                    'disabled' => 'disabled',
                    //'placeholder' => 'Enter page number',
                ],
                */
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