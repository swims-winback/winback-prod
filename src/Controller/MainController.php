<?php

namespace App\Controller;

//use App\Entity\Customer\User;
use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\DeviceServerRepository;
use App\Repository\SoftwareRepository;
use App\Repository\UserRepository;
//use App\Security\AppAuthenticator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MainController extends AbstractController
{
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

    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/", name="home")
     */
    public function index(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository, DeviceServerRepository $deviceServerRepository, DeviceRepository $deviceRepository, ChartBuilderInterface $chartBuilder, DeviceFamilyRepository $deviceFamilyRepository): Response
    {

        //$username = $this->getUser()->getUserIdentifier();
        $email = $this->getUser()->getUserIdentifier();
        //$user = $userRepository->findOneBy(array('email' => $email));
        //$email = $user->getEmail();
        $deviceConnected_array = $this->getDeviceServerCount($deviceServerRepository, $deviceFamilyRepository);
        ksort($deviceConnected_array);
        $deviceCreated = $this->getDeviceCreatedCount($deviceRepository, $deviceFamilyRepository);

        $deviceCount_array = $this->getDeviceCount($deviceFamilyRepository);

        //$firstDataset = [$this->getDataset("Devices connected", array_values($deviceConnected_array))];
        $firstDataset = [];
        foreach ($deviceConnected_array as $key => $value) {
            $firstDataset[] = $this->getDataset($key, array_values($value));

        }
        $secondDataset = [];
        foreach ($deviceCreated as $key => $value) {
            $secondDataset[] = $this->getDataset($key, array_values($deviceCreated[$key]));
        }
        $thirdDataset = [$this->getDataset('label', array_values($deviceCount_array))];
        // TODO
        $week = $this->getDateName($this->getDate());
        $firstChart = $this->getChart($chartBuilder, $week, $firstDataset, "Devices connected per week", Chart::TYPE_LINE);
        $secondChart = $this->getChart($chartBuilder, $week, $secondDataset, "Devices created per week", Chart::TYPE_LINE);
        $thirdChart = $this->getChart($chartBuilder, array_keys($deviceCount_array), $thirdDataset, 'Number of devices per type', Chart::TYPE_DOUGHNUT);

        return $this->render('main/index.html.twig', [
            /*
            'deviceConnectedArray'=>$deviceConnected_array,
            'deviceCreatedArray'=>$deviceCreated_array,
            'snArray'=>$snArray,
            */
            // TODO
            'firstChart'=>$firstChart,
            'secondChart'=>$secondChart,
            'thirdChart'=>$thirdChart,
            
        ]);
    }

    function getDate() {
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

    function getDateName($date) {
        $dateName = [];
        foreach ($date as $key => $value) {
            $dateName[] = date('D', strtotime($value));
        }
        return $dateName;
    }
    /**
     * @Route("/devicesConnected/", name="get_devices_connected")
     */
    
    function getDeviceServer(DeviceServerRepository $deviceServerRepository) {
        $date_array = $this->getDate();
        foreach ($date_array as $date) {
            $allDevices = $deviceServerRepository->findByDate($date);
            $deviceCount_array[$date] = count($allDevices);
        }
        return ($deviceCount_array);
    }
    
    function getDeviceServerCount(DeviceServerRepository $deviceServerRepository, DeviceFamilyRepository $deviceFamilyRepository) {
        $date_array = $this->getDate();
        $devicesFamily = $deviceFamilyRepository->findAll();
        $sortedDevices = [];
        foreach ($date_array as $date) {
            $allDevices = $deviceServerRepository->findByDate($date);
            foreach ($allDevices as $device) {
                //$sortedDevices[$device->getDevice()->getDeviceFamily()->getName()] = $device->getDevice()->getSn();
                
                $sortedDevices[$device->getDevice()->getDeviceFamily()->getName()][$date][] = $device->getDevice()->getSn();

                
            }
            
        }
        
        $countSortedDevices = [];
        foreach ($sortedDevices as $date => $dateValue) {
            foreach ($sortedDevices[$date] as $deviceType => $value) {
                $countSortedDevices[$date][$deviceType] = count($sortedDevices[$date][$deviceType]);
            }
        }
        

        return ($countSortedDevices);
    }
    /**
     * @Route("/devicesCreated/", name="get_devices_created")
     */
    function getDeviceCreated(DeviceRepository $deviceRepository) {
        $date_array = $this->getDate();
        foreach ($date_array as $date) {
            $allDevices = $deviceRepository->findByDate($date);
            $deviceCount_array[$date] = count($allDevices);
        }
        return ($deviceCount_array);
    }

    function getDeviceCreatedCount(DeviceRepository $deviceRepository, DeviceFamilyRepository $deviceFamilyRepository) {
        $date_array = $this->getDate();
        $devicesFamily = $deviceFamilyRepository->findAll();
        for ($i = 0; $i < sizeof($devicesFamily); $i++) {
            foreach ($date_array as $date) {
                $allDevices = $deviceRepository->findByDate($date, $devicesFamily[$i]->getId());
                $device_array[$date] = count($allDevices);
            }
            $deviceCountArray[$devicesFamily[$i]->getName()] = $device_array;
        }
        ksort($deviceCountArray);
        return ($deviceCountArray);
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
    function getChart(ChartBuilderInterface $chartBuilder, $labels, $datasets, $text, $chartType) {

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
                    'text'=> $text
                ]
            ],
            'responsive' => true,
            'maintainAspectRatio' => true,
            'scales'=> [
                'y'=> [
                    'beginAtZero'=> true,
                ]
            ]
        ]);

        return $chart;
    }

    function getDataset($label, $dataArray) {
        $backgroundArray = $this->backgroundArray;
        $borderArray = $this->borderArray;
        $dataset =
            [
                'label' => $label,
                'backgroundColor' => $backgroundArray,
                'borderColor' => $borderArray,
                'data' => $dataArray,
            ];
        return $dataset;

    }

    public function getDeviceCount(DeviceFamilyRepository $deviceFamilyRepository) {
        $devicesFamily = $deviceFamilyRepository->findAll();
        for ($i=0; $i < sizeof($devicesFamily); $i++) {
            $deviceCountArray[$devicesFamily[$i]->getName()] = count($devicesFamily[$i]->getDevices());
        }
        ksort($deviceCountArray);
        return $deviceCountArray;
    }

    public function dashboard(DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, DeviceFamilyRepository $deviceFamilyRepository) {
        $devicesFamily = $deviceFamilyRepository->findAll();
        $devices = $deviceRepository->findAll();
        $countDevices = count($devices);
        $deviceArray = array();
        
        foreach ($devicesFamily as $deviceFamily) {
            $softwares = $softwareRepository->findBy(
                array('deviceFamily'=>$deviceFamily),
                array('name' => 'DESC'));
            $softwareArray[$deviceFamily->getName()] = $softwares;
        }

        return $this->render('dashboard.html.twig', [
            'devicesFamily'=> $devicesFamily,
            'countDevices'=> $countDevices,
            'deviceArray'=> $deviceArray,
            'softwareArray'=> $softwareArray,
        ]);
    }

    /**
     * @Route("/version/{deviceFamily}/{version}", name="get_version")
     */
    public function getVersion($deviceFamily, $version, DeviceRepository $deviceRepository, DeviceFamilyRepository $deviceFamilyRepository)
    {
        $deviceFamilyId = $deviceFamilyRepository->findFamilyByName($deviceFamily)->getId();
        $devices = $deviceRepository->findBy(array('version' => $version, 'deviceFamily' => $deviceFamilyId));
        return new Response(count($devices));
    }
}