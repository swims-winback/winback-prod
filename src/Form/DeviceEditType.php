<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\DeviceFamily;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*
            ->add('type', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'numberId',
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            /*
            ->add('sn', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            //->add('deviceFamily', TextType::class)
            /*
            ->add('deviceFamily', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'name',
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            /*
            ->add('version', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            ->add('versionUpload', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
            ])
            /*
               ->add('versionUpload', ChoiceType::class, [
                //'label' => 'Max result: ',
                'choices'  => [
                    '2.8' => 2.8,
                    '2.5' => 2.5,
                    '5.0' => 5.0,
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
/*              ->add('versionUpload', EntityType::class, [
                'class' => Software::class,
                'choice_label' => 'version',
            ]) */
            //->add('forced', TextType::class)
            /*
            ->add('ipAddr', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            /*
            ->add('logPointeur', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            /*
            ->add('pub', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            /*
            ->add('codePin', TextType::class, [
                'attr' => [
                    'readonly' => 'readonly',
                    'class' => 'form-control'
                ],
            ])
            */
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn bg-orange',
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Device::class,
        ]);
    }
}                