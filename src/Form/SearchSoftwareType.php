<?php

namespace App\Form;

use App\Entity\DeviceFamily;
use App\Repository\DeviceFamilyRepository;
use App\Repository\SoftwareRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSoftwareType extends AbstractType
{
    public $registry;

    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }
    function getVersions(SoftwareRepository $softwareRepository) {
        $distinctCategories = $softwareRepository->distinctVersions();
        foreach ($distinctCategories as $cat) {
            if ($cat->getDeviceFamily()->getNumberId() == 10) {
                $sn_array['HI-TENS'] = $cat->getDeviceFamily()->getName();
            }
            elseif ($cat->getDeviceFamily()->getNumberId() == 14) {
                $sn_array['BACK3TX'] = $cat->getDeviceFamily()->getName();
            }
            else {
                $sn_array[$cat->getDeviceFamily()->getName()] = $cat->getDeviceFamily()->getName();
            }
        }
        return $sn_array;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $softwareRepository = new SoftwareRepository($this->registry);
        $deviceType_array = $this->getVersions($softwareRepository);
        $builder
            ->add('value', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Software'
                ],
                'required' => false,
            ])
            /*
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
            */
            ->add('category', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'multiple' => false,
                'choices'  => 
                    $deviceType_array
                ,
                'required' => false,
                'placeholder' => '',
                
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
