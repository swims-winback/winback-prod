<?php

namespace App\Form;

use App\Entity\DeviceFamily;
use App\Repository\DeviceFamilyRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSoftwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Software'
                ],
                'required' => false,
            ])
              ->add('category', EntityType::class, [
                'class' => DeviceFamily::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'query_builder' => function (DeviceFamilyRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            /*
            ->add('max_result', ChoiceType::class, [
                'label' => 'Max result: ',
                'choices'  => [
                    '10' => 10,
                    '25' => 25,
                    '50' => 50,
                ],
                'required' => false,
            ]) 
            */
            /*
            ->add('Search', SubmitType::class, [
                'attr' => [
                    'class' => 'btn bg-orange',
                ]
            ])
            */

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Device::class,
        ]);
    }
}
