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
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ProtocolController extends AbstractController
{
    public $backgroundArray = [
        'rgba(255, 99, 132, 0.7)',
        'rgba(255, 159, 64, 0.7)',
        'rgba(255, 205, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(54, 162, 235, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(201, 203, 207, 0.7)'
    ];
      
    public $borderArray = [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
      ];

      public $borderMode = [
        '#fe8c22',
        '#391fff',
        '#00fff7',
        '#6EB0FF',
        '#3DD931',
        '#B7E007',
        '#C9CBCF'
    ];
    public $backgroundMode = [
        '#fe8c22cc',
        '#391fff',
        '#00fff7a2',
        '#6EB0FFa2',
        '#3DD931a2',
        '#B7E007a2',
        '#C9CBCFa2'
    ];

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

    public $acc_name = [
        0=>"NO_CONNECTED",
        1=>"TECARX_RET_40",
        2=>"TECARX_RET_60",
        3=>"TECARX_RET_70",
        4=>"TECARX_CET_40",
        5=>"TECARX_CET_60",
        6=>"TECARX_CET_70",
        7=>"TECARX_CET_60_CVX",
        8=>"TECARX_MIX_BODY",
        9=>"TECARX_MIX_FACE",
        10=>"TECARX_RET_40_CVX",
        11=>"TECARX_T6", # Hi-ret
        12=>"TECAR3_RET_40",
        13=>"TECAR3_RET_60",
        14=>"TECAR3_RET_70",
        15=>"TECAR3_CET_40",
        16=>"TECAR3_CET_60",
        17=>"TECAR3_CET_70",
        18=>"TECAR3_CET_60_CVX",
        19=>"TECAR3_RET_40_CVX",
        20=>"TECAR3_HYB_40",
        21=>"TECAR3_HYB_60",
        22=>"TECAR3_T6", # Hi-ret
        23=>"TECAR6",
        24=>"MIX_FACE",
        25=>"MIX_BODY",
        26=>"ADHESIVE_RET",
        28=>"RET_BRACELET",
        29=>"STIM_CABLE",
        30=>"CET_FIXPAD",
        31=>"RET_FIXPAD",
        32=>"TECAR3_CET_40_CVX",
        33=>"TECARX_CET_40_CVX",
        34=>"EXTENDER_CET",
        35=>"EXTENDER_RET"
    ];

    #[Route('/{_locale<%app.supported_locales%>}/protocol', name: 'app_protocol')]
    public function index(ProtocolRepository $protocolRepository, Request $request, ChartBuilderInterface $chartBuilder): Response
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
        

        $week = $this->getWeek();
        $weekName = $this->getWeekName($week);
        // utilisation mode / jour / semaine
        $firstDataset = [];
        $modeRandom = $this->getModeUseRandom();
        foreach ($modeRandom as $modeKey => $modeValue) {
            foreach ($modeRandom[$modeKey] as $key => $value) {
                $firstDataset[] = $this->getDataset($this->modes_name[$modeKey]['name'], array_values($value), $this->backgroundMode[$modeKey], $this->borderMode[$modeKey]);
            }
        }
        $firstChart = $this->getChart($chartBuilder, $weekName, $firstDataset, 'text', Chart::TYPE_BAR, 'percentage');
        // utilisation mode / semaine
        $modeWeek = $this->getModeUseRandomWeek();
        //var_dump($modeWeek);
        foreach ($modeWeek as $key => $value) {
            //var_dump(array_values($value)[0]);
            //$firstDatasetWeek = [$this->getDataset(array_keys($modeWeek), array_values($modeWeek), array_values($this->backgroundMode), array_values($this->borderMode))];
            //$firstDatasetWeek[] = $this->getDataset(array_keys($value)[0], array_values($value)[0], $this->backgroundMode[$key], $this->borderMode[$key]);
            $firstLabels[] = array_keys($value)[0];
            $firstData[] = array_values($value)[0];
        }

        //var_dump($firstDatasetWeek);
        $firstDatasetWeek = $this->getDataset('mode', $firstData, $this->backgroundMode, $this->borderMode);
        $firstChartWeek = $this->getChart($chartBuilder, $firstLabels, $firstDatasetWeek, 'text', Chart::TYPE_BAR, '', false);
        /*
        $firstChartWeek = $chartBuilder->createChart(Chart::TYPE_BAR);
        $firstChartWeek->setData([
            'labels' => $firstLabels,
            'datasets' =>  [[
                'label' => ['mode'],
                'backgroundColor' => $this->backgroundMode,
                'borderColor' => $this->borderMode,
                'data' => $firstData,
            ]],   
        ]);
    
        $firstChartWeek->setOptions([
            'plugins'=> [
                'title'=> [
                    'display'=> true,
                ],
                'legend'=>[
                    'display'=>false,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => true,
            'scales'=> [
                'y'=> [
                    'beginAtZero'=> true,
                    'display'=>true,
                ]
            ]
        ]);
        */
        // utilisation accessoire / semaine
        $secondDataset = [];
        $accRandom = $this->getAccUseRandom();
        
        foreach ($accRandom as $modeKey => $modeValue) {
                //var_dump(array_values($modeValue));
                if ($modeKey > 6) {
                    $secondDataset[] = $this->getDataset($this->acc_name[$modeKey], array_values($modeValue), $this->backgroundArray[random_int(0, 6)], $this->borderArray[random_int(0, 6)]);
                }
                else {
                    $secondDataset[] = $this->getDataset($this->acc_name[$modeKey], array_values($modeValue), $this->backgroundArray[$modeKey], $this->borderArray[$modeKey]);
                }
        }

        $secondChart = $this->getChart($chartBuilder, $weekName, $secondDataset, 'text', Chart::TYPE_BAR);

        $treatmentRandom = $this->getTreatmentRandom();

        $thirdDataset = [$this->getDataset($weekName, array_values($treatmentRandom), $this->backgroundArray, $this->borderArray)];
        $thirdChart = $this->getChart($chartBuilder, array_values($weekName), $thirdDataset, 'text', Chart::TYPE_BAR);

        // average treatment duration per week
        $treatmentDurationDataset = [$this->getDataset('label', [22, 60-22], [$this->backgroundArray[0], 'rgba(201, 203, 207, 0.2)'], [$this->borderArray[0], 'rgba(201, 203, 207, 0.2)'])];
        $treatmentDurationChart = $this->getChart($chartBuilder, [22, 60-22], $treatmentDurationDataset, 'text', Chart::TYPE_DOUGHNUT, 'percen', false, false);
        return $this->render('protocol/index.html.twig', [
            'protocols' => $protocols_array,
            //'sn_array' => $sn_array,
            'searchForm' => $searchForm,
            'firstChart'=> $firstChart,
            'firstChartWeek'=> $firstChartWeek,
            'secondChart'=> $secondChart,
            'thirdChart'=> $thirdChart,
            'treatmentDurationChart' => $treatmentDurationChart,
        ]);
    }

    function getWeek() {
        //find 7 days before today
        $i = 0;
        $date_array = [];
        $deviceCount_array = [];
        while ($i <= 7) {
            $date = strtotime("-{$i} day");
            $date_array[] = date('Y-m-d', $date);
            $i++;
        }
        $date_array = array_reverse($date_array);
        return $date_array;
    }

    function getWeekName($date) {
        $dateName = [];
        foreach ($date as $key => $value) {
            $dateName[] = date('D', strtotime($value));
        }
        return $dateName;
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

    function generateRandomPercentages($count) {
        $percentages = [];
    
        // Generate random values for each element except the last one
        for ($i = 0; $i < $count - 1; $i++) {
            $randomPercentage = rand(1, 100 - array_sum($percentages));
            $percentages[] = $randomPercentage;
        }
    
        // Calculate the last element to ensure the total is 100
        $lastPercentage = 100 - array_sum($percentages);
        $percentages[] = $lastPercentage;
    
        // Shuffle the array to randomize the order
        shuffle($percentages);
    
        return $percentages;
    }

    
    function getModeUseRandom() {
        $week = $this->getWeek();
        $modeRandom = [];

        foreach ($this->modes_name as $keyMode => $valueMode) {
            foreach ($week as $key => $value) {
                $modeRandom[$keyMode][$valueMode['name']][$value] = random_int(0, 50);
                //$modeRandom[$keyMode][$valueMode['name']][$value] = $randomPercentages[$key];
            }
        }
        return $modeRandom;
    }

    function getModeUseRandomWeek() {
        //
        //$week = $this->getWeek();
        $modeRandom = [];

        foreach ($this->modes_name as $keyMode => $valueMode) {
            //foreach ($week as $key => $value) {
                $modeRandom[$keyMode][$valueMode['name']] = random_int(0, 50);
                //$modeRandom[$keyMode][$valueMode['name']][$value] = $randomPercentages[$key];
            //}
        }
        return $modeRandom;
    }

    function getAccUseRandom() {
        $week = $this->getWeek();
        $modeRandom = [];
        $acc_name = array_rand($this->acc_name, 5);


        foreach ($acc_name as $keyMode => $valueMode) {
            // Number of elements in the array
            $numberOfElements = 5;
            // Generate the array
            $randomPercentages = $this->generateRandomPercentages($numberOfElements);
            // Display the result
            //var_dump($randomPercentages);
            foreach ($week as $key => $value) {
                
                //print_r($key);
                $modeRandom[$valueMode][$value] = random_int(0, 50);
                //$modeRandom[$valueMode][$value] = $randomPercentages[$keyMode];
            }
        }
        return $modeRandom;
    }

    function getTreatmentRandom() {
        $week = $this->getWeek();
        foreach ($week as $key => $value) {

            $modeRandom[$value] = random_int(0, 50);
        }
        return $modeRandom;
    }

    /**
     * getChart
     * @param ChartBuilderInterface $chartBuilder
     * @param array $labels - array of legends
     * @param string $label
     * @param array $dataArray - array of values
     * @param string $text
     * @return Chart
     */
    function getChart(ChartBuilderInterface $chartBuilder, $labels, $datasets, $text, $chartType, $textY='percen', $displayLeg=true, $displayY=true) {
        //$backgroundArray = $this->backgroundArray;
        //$borderArray = $this->borderArray;
        //Chart::TYPE_LINE
        $chart = $chartBuilder->createChart($chartType);
        $chart->setData([
            'labels' => $labels,
            'datasets' => $datasets,    
        ]);
    
        $chart->setOptions([
            
            'plugins'=> [
                'title'=> [
                    'display'=> true,
                    //'text'=> $text
                ],
                'legend'=>[
                    'display'=>$displayLeg,
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => true,
            'scales'=> [
                'y'=> [
                    'beginAtZero'=> true,
                    'display'=>$displayY,
                    'text'=>$textY
                ]
            ]
        ]);

        return $chart;
    }

    function getDataset($label, $dataArray, $backgroundArray, $borderArray) {
        //$backgroundArray = $this->backgroundArray;
        //$borderArray = $this->borderArray;
        $dataset =
            [
                'label' => $label,
                'backgroundColor' => $backgroundArray,
                'borderColor' => $borderArray,
                'data' => $dataArray,
            ];
        return $dataset;

    }
}
