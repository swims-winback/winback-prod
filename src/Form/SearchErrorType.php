<?php

namespace App\Form;

use App\Class\SearchError;
use App\Entity\Main\Device;
use App\Entity\Main\Error;
use App\Entity\Main\ErrorFamily;
use App\Repository\DeviceRepository;
use App\Repository\ErrorFamilyRepository;
use App\Repository\ErrorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchErrorType extends AbstractType
{
    public $registry;

    public function __construct(ManagerRegistry $registry) {
        $this->registry = $registry;
    }
    /*
    function getCategories(ErrorRepository $errorRepository) {
        $distinctCategories = $errorRepository->distinctCategories();
        foreach ($distinctCategories as $cat) {
            $sn_array[$cat->getSn()->getSn()] = $cat->getSn()->getSn();
        }
        return $sn_array;
    }
    */
    function getCategories(ErrorRepository $errorRepository) {
        foreach ($errorRepository->distinctDeviceType() as $key => $value) {
            $deviceTypes[$value["deviceType"]] = $value["deviceType"];
        }
        return $deviceTypes;
    }
    function getErrors(ErrorRepository $errorRepository) {
        $distinctCategories = $errorRepository->distinctErrors();
        foreach ($distinctCategories as $cat) {
            $sn_array[$cat->getError()->getErrorId()] = $cat->getError()->getErrorId();
        }
        return $sn_array;
    }
    // get versions by deviceType
    function getVersions(ErrorRepository $errorRepository) {
        foreach ($errorRepository->distinctDeviceType() as $key => $value) {
            $deviceTypes[$value["deviceType"]] = $value["deviceType"];
        }
        foreach ($deviceTypes as $key => $value) {
            $distinctCategories = $errorRepository->distinctVersions($key);
            foreach ($distinctCategories as $cat) {
                $sn_array[$key][$cat->getVersion()] = $cat->getVersion();
            }
        }
        return $sn_array;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $errorRepository = new ErrorRepository($this->registry);
        $sn_array = $this->getCategories($errorRepository);
        $error_array = $this->getErrors($errorRepository);
        $version_array = $this->getVersions($errorRepository);
        
        $builder
            
            ->add('q', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Search',
                ],
                'required' => false,
            ])
            
            ->add('sn_category', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control dropdown-menu filterDropdown-content px-2 text-center'
                ],
                'multiple' => true,
                'expanded' => true,
                'choices'  => [
                    'Serial Numbers'=>$sn_array
                ],
                'required' => false,
                
            ])
            ->add('error_category', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control dropdown-menu filterDropdown-content px-2 text-center',
                ],
                'multiple' => true,
                'expanded' => true,
                'choices'  => 
                [
                    ''=>$error_array
                ]
                ,
                'required' => false,
                
            ])

            ->add('version', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control dropdown-menu filterDropdown-content px-2 text-center',
                ],
                'multiple' => true,
                'expanded' => true,
                'choices'  => [
                    "BACK3TE" => [$version_array["BACK3TE"]], 
                    "BACK3TX"=>[$version_array["BACK3TX"]], 
                    "BACK4" => [$version_array["BACK4"]], 
                    "NEOCARE ELITE"=>[$version_array["NEOCARE ELITE"]]
                    ]
                ,
                'required' => false,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchError::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}