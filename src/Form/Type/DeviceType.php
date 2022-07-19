<?php

namespace App\Form\Type;

use App\Entity\Device;
use App\Entity\DeviceFamily;
use App\Entity\DeviceVersion;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*
            ->add('type', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'numberId',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
            ->add('sn', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])

            /*
            ->add('version', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
            
            ->add('version', EntityType::class, [
                'class' => Software::class,
                'choice_label' => 'version',
                'attr' => [
                    'class' => 'form-control'
                ],
                //'mapped' => false
            ])
            
            /*
            ->add('version', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'software',
                'attr' => [
                    'class' => 'form-control'
                ],
                //'mapped' => false
            ])
            */
            //->add('deviceFamily', TextType::class)
            
            ->add('deviceFamily', EntityType::class, [
                'class' => DeviceFamily::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            /*
            ->add('deviceFamily', EntityType::class, [
                'class' => DeviceFamily::class,
                'placeholder' => '',
            ])
            */
            /*
            ->add('deviceFamily', EntityType::class, [
                'class' => Software::class,
                'choice_label' => 'family',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
            
            //->add('versionUpload', TextType::class)
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
            

            /*
              ->add('versionUpload', EntityType::class, [
                'class' => Software::class,
                'choice_label' => 'version',
            ])
            */

            //->add('forced', TextType::class)
            ->add('ipAddr', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('logPointeur', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('pub', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('codePin', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            /*
            ->add('file', FileType::class, [
                'label' => 'Device file',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            */
            
            
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn bg-orange',
                ]
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
