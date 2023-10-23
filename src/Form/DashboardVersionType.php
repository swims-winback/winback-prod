<?php

namespace App\Form;

use App\Class\SearchVersion;
use App\Entity\Main\Device;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DashboardVersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('versions', EntityType::class, [
            'class' => Device::class,
            'label' => false,
            'attr' => [
                'class' => 'form-control',
            ],
            'required' => false,
            'expanded' => true,
            'multiple' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchVersion::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}