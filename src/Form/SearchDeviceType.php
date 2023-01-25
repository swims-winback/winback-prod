<?php

namespace App\Form;

use App\Class\SearchData;
use App\Entity\DeviceFamily;
use App\Entity\Software;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDeviceType extends AbstractType
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
            
            
            ->add('categories', EntityType::class, [
                'class' => DeviceFamily::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            
            ->add('version', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter version',
                ],
                'required' => false,
            ])
            
            ->add('version_upload', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter upload version',
                ],
                'required' => false,
            ])
            
            ->add('forced', CheckboxType::class, [
                'label' => 'Forced',
                'required' => false,
            ])
            /*
            ->add('connected', CheckboxType::class, [
                'label' => 'Connected',
                'required' => false,
            ])
            */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
