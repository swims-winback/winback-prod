<?php

namespace App\Form;

use App\Class\SearchData;
use App\Entity\DeviceFamily;
use App\Entity\Software;
use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
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
    public $registry;
    public $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator) {
        $this->registry = $registry;
        $this->paginator = $paginator;
    }

    function getVersions(DeviceRepository $deviceRepository) {
        $distinctCategories = $deviceRepository->distinctVersions();
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

    function getCountries(DeviceRepository $deviceRepository) {
        $distinctCategories = $deviceRepository->distinctCountries();
        foreach ($distinctCategories as $cat) {
            if ($cat->getCountry() != '0') {
                $result_array[$cat->getCountry()] = $cat->getCountry();
            }
            
        }
        return $result_array;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $deviceRepository = new DeviceRepository($this->registry, $this->paginator);
        $deviceType_array = $this->getVersions($deviceRepository);
        $country_array = $this->getCountries($deviceRepository);
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
            ->add('categories', EntityType::class, [
                'class' => DeviceFamily::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
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
                'choices'  => $deviceType_array
                ,
                'required' => false,
                'placeholder' => '',
                
            ])
            
            ->add('version', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Version',
                ],
                'required' => false,
            ])
            
            ->add('version_upload', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Upload Version',
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

            ->add('country', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'multiple' => false,
                'choices'  => $country_array
                ,
                'required' => false,
                'placeholder' => false,
                
            ])
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
