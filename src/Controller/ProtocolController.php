<?php

namespace App\Controller;

use App\Repository\ProtocolRepository;
use SearchProtoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProtocolController extends AbstractController
{
    public $modes_name = [
        0=>[
            'name'=>'CET',
            'param2'=>[
                0=>'SOFT',
                1=>'DYNAMIC',
                2=>'DEEP'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        1=>[
            'name'=>'RET',
            'param2'=>[
                0=>'SOFT',
                1=>'DYNAMIC',
                2=>'DEEP'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        2=>[
            'name'=>'HI-TENS',
            'param2'=>[
                0=>'CHRONIC',
                1=>'DYNAMIC', //TODO vérifier avec Bastien
                2=>'MANUAL'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        3=>[
            'name'=>'HI-EMS',
            'param2'=>[
                0=>'RADIAL',
                1=>'DYNAMIC',
                2=>'FOCAL',
                3=>'DRAIN'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        4=>[
            'name'=>'MIX',
            'param2'=>[
                0=>'SOFT',
                5=>'5 Hz pulsed deep'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        5=>[
            'name'=>'BIOBACK',
            'param2'=>[
                0=>'SOFT',
                1=>'DYNAMIC',
                2=>'DEEP'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
        6=>[
            'name'=>'NEUTRAL',
            'param2'=>[
                0=>'ko',
                1=>'ok'
            ],
            'param3'=>[
                0=>'LOW',
                1=>'MEDIUM',
                2=>'BOOST',
            ]
        ],
    ];

    #[Route('/{_locale<%app.supported_locales%>}/protocol', name: 'app_protocol')]
    public function index(ProtocolRepository $protocolRepository, Request $request): Response
    {
        // choose sn array by client id
        $sn_array = $protocolRepository->findSN();
        foreach ($sn_array as $key => $value) {
            $sn_select_array[$sn_array[$key]['sn']] = $sn_array[$key]['sn'];
        }

        $searchForm = $this->createFormBuilder()
            ->add('sn', ChoiceType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices' => [
                    "Serial Numbers"=>$sn_select_array
                ],
                'placeholder' => 'Serial Numbers',
                
                'placeholder_attr' => [
                    'hidden' => 'hidden',
                    'disable'=> 'disable'
                ]
                
            ])

            ->getForm();

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $task = $searchForm->getData();
            $sn = $task['sn'];
            $dates = $this->getDate($protocolRepository, $sn);
    
            foreach ($dates as $key => $value) {
                $protocols = $this->getMode($protocolRepository, $value['date'], $sn);
                $protocols_array[$value['date']] = $protocols;
            }
        }
        else {
            $protocols_array = [];
        }


        return $this->render('protocol/index.html.twig', [
            'protocols' => $protocols_array,
            'sn_array' => $sn_array,
            'searchForm' => $searchForm
        ]);
    }

    public function getDate(ProtocolRepository $protocolRepository, $sn) {
        $date_array = $protocolRepository->findAllDate($sn);
        return $date_array;
    }

    public function getMode(ProtocolRepository $protocolRepository, $date, $sn) {
        $modeObj = $protocolRepository->findMode($sn, $date);
        
        foreach ($modeObj as $key => $value) {
            //var_dump($this->modes_name[$value->getModeId()]['param2'][$value->getParam2()]);
            //$mode_obj["protocol_id"][$value->getProtocolId()]["step_id"][$value->getStepId()]["way_id"][$value->getWayId()]["mode_id"][$this->modes_name[$value->getModeId()]['name']] = [$value->getFullDate(), $value->getParam1(), $value->getParam2(), $value->getParam3()];
            $mode_obj["protocol_id"][$value->getProtocolId()]["step_id"][$value->getStepId()]["way_id"][$value->getWayId()]["mode_id"][$this->modes_name[$value->getModeId()]['name']] = array(
                //"full_date" => $value->getFullDate(), 
                "param1" => $value->getParam1(),
                "param2" => $this->modes_name[$value->getModeId()]['param2'][$value->getParam2()], 
                "param3" => $this->modes_name[$value->getModeId()]['param3'][$value->getParam3()],
                "time_tot" => $value->getTimeTot());
        }
        //var_dump($mode_obj);
        return $mode_obj;
    }
    
}
