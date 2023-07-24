<?php

namespace App\Form;

use App\Class\SearchError;
use App\Entity\Device;
use App\Entity\Error;
use App\Entity\ErrorFamily;
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
    function getCategories(ErrorRepository $errorRepository) {
        $distinctCategories = $errorRepository->distinctCategories();
        foreach ($distinctCategories as $cat) {
            $sn_array[$cat->getSn()->getSn()] = $cat->getSn()->getSn();
        }
        return $sn_array;
    }
    function getErrors(ErrorRepository $errorRepository) {
        $distinctCategories = $errorRepository->distinctErrors();
        foreach ($distinctCategories as $cat) {
            $sn_array[$cat->getError()->getErrorId()] = $cat->getError()->getErrorId();
        }
        return $sn_array;
    }
    function getVersions(ErrorRepository $errorRepository) {
        $distinctCategories = $errorRepository->distinctVersions();
        foreach ($distinctCategories as $cat) {
            $sn_array[$cat->getVersion()] = $cat->getVersion();
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
                    'class' => 'form-control',
                ],
                'multiple' => true,
                'choices'  => [
                    'Serial Numbers'=>$sn_array
                ],
                'required' => false,
                
            ])
            ->add('error_category', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'multiple' => true,
                'choices'  => 
                [
                    ''=>$error_array
                ]
                ,
                'required' => false,
                
            ])
            /*
            ->add('error_category', EntityType::class, [
                'class' => ErrorFamily::class,
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (ErrorFamilyRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.error_id', 'ASC');
                },
            ])
            */
            /*
            ->add('version', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Version',
                ],
                'required' => false,
            ])
            */
            ->add('version', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'multiple' => true,
                'choices'  => [
                    'Versions'=>$version_array
                ],
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