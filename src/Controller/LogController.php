<?php

namespace App\Controller;

use App\Form\SearchLogType;
use App\Repository\LogRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * 
 * Protocole: Steps list defined by the Academy for 1 pathology.
 * Treatment: what 1 patient receives during 1 session. Starts when therapist triggers play button, ends when one of these events happen: "SON", "SOF", "ERR", "STP "
 *      Defined: 1 academy protocol
 *      Undefined: new protocol created by the therapist
 * Step: Element which compose a protocol, no parameter changes in the step (1 mode, 1 accessory…)
 */
class LogController extends AbstractController
{
    public $modes = [
        0 => 'Mode 1',
        1 => 'Mode 2',
    ];

    public $ways = [
        0 => 'Way 1',
        1 => 'Way 2',
    ];


    public $modes_name = [
        0=>[
            'name'=>'CET',
            'param2'=>[
                0=>'soft',
                1=>'dynamic',
                2=>'deep'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        1=>[
            'name'=>'RET',
            'param2'=>[
                0=>'soft',
                1=>'dynamic',
                2=>'deep'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        2=>[
            'name'=>'HI-TENS',
            'param2'=>[
                0=>'chronic',
                1=>'dynamic', //TODO vérifier avec Bastien
                2=>'manual'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        3=>[
            'name'=>'HI-EMS',
            'param2'=>[
                0=>'radial',
                1=>'dynamic',
                2=>'focal',
                3=>'drain'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        4=>[
            'name'=>'MIX',
            'param2'=>[
                0=>'soft',
                5=>'5 Hz pulsed deep'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        5=>[
            'name'=>'BIOBACK',
            'param2'=>[
                0=>'soft',
                1=>'dynamic',
                2=>'deep'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
        6=>[
            'name'=>'NEUTRAL',
            'param2'=>[
                0=>'ko',
                1=>'ok'
            ],
            'param3'=>[
                0=>'low',
                1=>'medium',
                2=>'boost',
            ]
        ],
    ];

    public $ways_name = [
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
        27=>"",
        28=>"RET_BRACELET",
        29=>"STIM_CABLE",
        30=>"CET_FIXPAD",
        31=>"RET_FIXPAD", # juste RET (???)
        32=>"TECAR3_CET_40_CVX",
        33=>"TECARX_CET_40_CVX",
        34=>"EXTENDER_CET",
        35=>"EXTENDER_RET"
    ];

    public $backgroundArray = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
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

    #[Route('/{_locale<%app.supported_locales%>}/admin/log', name: 'app_log')]
    public function index(LogRepository $logRepository, Request $request, ChartBuilderInterface $chartBuilder): Response
    {
        $modes = $this->modes;
        $ways = $this->ways;
        $modes_name = $this->modes_name;
        $ways_name = $this->ways_name;
        $form = $this->createForm(SearchLogType::class);
        $search = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sn = $search->get('sn')->getData();
            $date = $search->get('date')->getData();
        }


        $protocoles = [];
        $blocs = [];
        $bloc_id_array = [];

        $sn = 511;
        $date = "2023-03-29";
        //var_dump($sn);
        //var_dump($date);

        $logs = $logRepository->findBy(array('date' => $date, 'serial_number' => $sn));

        $blocsInTable = $logRepository->findAll();
        foreach ($blocsInTable as $key => $value) {
            $bloc_id_array[] = $value->getBlocId();
        }
        
        $bloc_id_array = array_values(array_unique($bloc_id_array));

        //Pour chaque protocole ("bloc" dans mon code)
        foreach ($bloc_id_array as $key => $bloc_id) {
            // Trouver toutes les steps correspondants à l'id bloc

            $bloc = $logRepository->findBy(
                array('bloc_id' => $bloc_id)
            );

            /* TEST */
            $protocole = [];
            // Pour chaque step
            foreach ($bloc as $b) {
                $protocole[$b->getStepsId()]["way1"]["acc"] = $ways_name[$b->getWay1Acc()];
                $protocole[$b->getStepsId()]["way2"]["acc"] = $ways_name[$b->getWay2Acc()];

                # MODE 1
                
                if ($b->getMode1Id() != '') { // check if defined
                    $protocole[$b->getStepsId()]["mode1"][$b->getMode1Id()]['name'] = $modes_name[$b->getMode1Id()]['name']; // search ID in mode array and replace by corresponding string
                }
                if ($b->getMode1Intensite() != '') {
                    $protocole[$b->getStepsId()]["mode1"][$b->getMode1Id()]['param1'] = $b->getMode1Intensite(); #intensity
                }
                if ($b->getMode1Param2() != '') {
                    $protocole[$b->getStepsId()]["mode1"][$b->getMode1Id()]['param2'] = $modes_name[$b->getMode1Id()]['param2'][$b->getMode1Param2()];
                }
                if ($b->getMode1Param3() != '') {
                    $protocole[$b->getStepsId()]["mode1"][$b->getMode1Id()]['param3'] = $modes_name[$b->getMode1Id()]['param3'][$b->getMode1Param3()];
                }
                # MODE 2
                
                if ($b->getStepsId() != '') {
                    $protocole[$b->getStepsId()]["mode2"][$b->getMode2Id()]['name'] = $modes_name[$b->getMode2Id()]['name'];
                }
                if ($b->getMode2Intensite() != '') {
                    $protocole[$b->getStepsId()]["mode2"][$b->getMode2Id()]['param1'] = $b->getMode2Intensite(); #intensity
                }
                if ($b->getMode2Param2() != '') {
                    $protocole[$b->getStepsId()]["mode2"][$b->getMode2Id()]['param2'] = $modes_name[$b->getMode2Id()]['param2'][$b->getMode2Param2()];
                }
                if ($b->getMode2Param3() != '') {
                    $protocole[$b->getStepsId()]["mode2"][$b->getMode2Id()]['param3'] = $modes_name[$b->getMode2Id()]['param3'][$b->getMode2Param3()];
                }

                $protocole[$b->getStepsId()]["timeTot"] = $b->getTimeContact();
                
                $protocole[$b->getStepsId()]["time"] = intdiv(intval($b->getTimeContact()),60);
                $protocole[$b->getStepsId()]["date"] = $b->getTime();
            }
            #var_dump($protocole);
            $protocole_reindex = array_values($protocole);
            $protocoles[$bloc_id] = $protocole_reindex;
            //var_dump(array_keys($protocoles[$bloc_id]));

            /* TEST */

            /*
            // Pour chaque step
            $mode1_id_array=[];
            $mode2_id_array=[];
            $way1Acc_array=[];
            $way2Acc_array=[];

            foreach ($bloc as $b) {
                $mode1_id_array[] = $b->getMode1Id();
                $mode2_id_array[] = $b->getMode2Id();
                $way1Acc_array[] = $b->getWay1Acc();
                $way2Acc_array[] = $b->getWay2Acc();
            }

            $mode1_id_array = array_filter($mode1_id_array);
            $mode2_id_array = array_filter($mode2_id_array);
            
            $c_way1Acc = $this->getCount($way1Acc_array, $ways_name);
            $c_way2Acc = $this->getCount($way2Acc_array, $ways_name);
            $c_mode1Id = $this->getCount($mode1_id_array, $modes_name);
            $c_mode2Id = $this->getCount($mode2_id_array, $modes_name);

            $p_way1Acc = $this->getPercent($c_way1Acc);
            $p_way2Acc = $this->getPercent($c_way2Acc);
            $p_mode1Id = $this->getPercent($c_mode1Id);
            $p_mode2Id = $this->getPercent($c_mode2Id);
            
            $blocArray["bloc_id"] = $bloc_id;
            $blocArray["way1Acc"] = $p_way1Acc;
            $blocArray["way2Acc"] = $p_way2Acc;
            $blocArray["mode1Id"] = $p_mode1Id;
            $blocArray["mode2Id"] = $p_mode2Id;

            $mode2_param = [];
            foreach ($blocArray["mode2Id"] as $mode2Id => $value) {
                $modes = $logRepository->findBy(
                    array('bloc_id' => $bloc_id, 'mode2_id' => $mode2Id)
                );
                $mode2_intensite_array = [];
                $mode2_param2_array = [];
                $mode2_param3_array = [];
                foreach ($modes as $m) {
                    $mode2_intensite_array[] = $m->getMode2Intensite();
                    $mode2_param2_array[] = $m->getMode2Param2();
                    $mode2_param3_array[] = $m->getMode2Param3();

                }
                $c_mode2_intensite = $this->getCount($mode2_intensite_array);
                $p_mode2_intensite = $this->getPercent($c_mode2_intensite);
                $c_mode2_param2 = $this->getCount($mode2_param2_array);
                $p_mode2_param2 = $this->getPercent($c_mode2_param2);
                $c_mode2_param3 = $this->getCount($mode2_param3_array);
                $p_mode2_param3 = $this->getPercent($c_mode2_param3);
                
                $modeArray["mode_id"] = $mode2Id;
                $modeArray["intensite"] = $p_mode2_intensite;
                $modeArray["param2"] = $p_mode2_param2;
                $modeArray["param3"] = $p_mode2_param3;
                $mode2_param[] = $modeArray;
            }
            $blocArray["mode2Param"] = $mode2_param;
            
            // on veut un chart par mode, par bloc: s'il y a deux modes, il y a deux charts
            $modeCharts = [];
            $charts = [];
            foreach ($blocArray["mode2Param"] as $key => $value) {
                if (!empty($blocArray["mode2Param"]) && !empty(array_keys($blocArray["mode2Param"][$key]["intensite"]))) {
                    $text = $modes_name[$blocArray["mode2Param"][$key]["mode_id"]];
                    $modeChart = $this->getChart($chartBuilder, array_keys($blocArray["mode2Param"][$key]["intensite"]), "label", array_values($blocArray["mode2Param"][$key]["intensite"]), $text);
                    $modeCharts[] = $modeChart;
                }
            }
            $blocArray["modeCharts"] = $modeCharts;
            
            $blocs[] = $blocArray;
            //calculate param of mode per modeId
            //show results in graph

            $new_modes_names = [];
            foreach ($modes_name as $key => $value) {
                if (array_key_exists($key, array_keys($blocArray["mode1Id"]))) {
                    $new_modes_names[$key] = $value;
                }
            }
            $text = 'Bloc '.$bloc_id;
            if (array_values($blocArray["mode1Id"])) {
                $chart = $this->getChart($chartBuilder, $new_modes_names, $bloc_id, array_values($blocArray["mode1Id"]), $text);
                $charts[] = $chart;
            }

            $new_way2Acc_names = []; // Get accessory name for corresponding ID
            foreach ($ways_name as $key => $value) {
                if (array_key_exists($key, array_keys($blocArray["way2Acc"]))) {
                    $new_way2Acc_names[$key] = $value;
                }
            }

            if (array_values($blocArray["way2Acc"])) {
                $way2AccChart = $this->getChart($chartBuilder, $new_way2Acc_names, $bloc_id, array_values($blocArray["way2Acc"]), $text);
                $way2AccCharts[] = $way2AccChart;
            }
            */
        }

        return $this->render('log/index.html.twig', [
            'controller_name' => 'LogController',
            //'logs' => $logs,
            'blocs' => $blocs,
            //'charts' => $charts,
            //'way2AccCharts' => $way2AccCharts,
            'protocoles'=> $protocoles,
            //'modeChartsBloc' => $modeChartsBloc
            //'blocArray' => $blocArray
            //'form' => $form->createView(),
            'modes'=>$modes,
            'ways'=>$ways,
            'modes_name'=>$modes_name,
            'ways_name'=>$ways_name
        ]);
    }

    /**
     * Count occurences of values in array and return A-value as key & N-occurences as value
     * @param array $elem_array
     * @param mixed $id_array
     * @return array
     */
    function getCount($elem_array, $id_array='') {
        $elem_uniq = array_unique($elem_array);
        $elem_count = array_count_values($elem_array);
        $c=array_combine($elem_uniq,$elem_count);
        return $c;
    }

    /**
     * Compute the percentage of each value in array
     * @param mixed $array
     * @return array $resultArray
     */
    function getPercent($array) {
        $total=array_sum($array);
        $resultArray = array();
        foreach ($array as $key => $value) {
            $resultArray[$key] = round($value / $total *100); //calculation
        }
        return $resultArray;
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
    function getChart(ChartBuilderInterface $chartBuilder, $labels, $label, $dataArray, $text) {
        $backgroundArray = $this->backgroundArray;
        $borderArray = $this->borderArray;
        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $label,
                    'backgroundColor' => $backgroundArray,
                    'borderColor' => $borderArray,
                    'data' => $dataArray,
                ],
            ],
        ]);
    
        $chart->setOptions([
            'plugins'=> [
                'title'=> [
                    'display'=> true,
                    'text'=> $text
                ]
            ]
        ]);
        return $chart;
    }
}
