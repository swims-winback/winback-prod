<?php

namespace App\Form;

use App\Entity\Main\DeviceFamily;
use App\Entity\Main\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SoftwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('version', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('family', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
            ->add('file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                 'constraints' => [
                    new File([
                        'maxSize' => '40M',
                        /*
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        */
                        'mimeTypesMessage' => 'Please upload a valid bin document',
                    ])
                ],
            ])
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-orange btn-outline-orange'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Software::class,
        ]);
    }
}
