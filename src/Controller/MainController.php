<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\ClientSn;
use App\Form\Client\ClientRegistrationType;
use App\Repository\ClientRepository;
use App\Repository\ClientSnRepository;
use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\DeviceServerRepository;
use App\Repository\SoftwareRepository;
use App\Repository\UserRepository;
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
    public function index(Request $request, ManagerRegistry $doctrine, ClientRepository $clientRepository, UserRepository $userRepository, DeviceServerRepository $deviceServerRepository, DeviceRepository $deviceRepository, ClientSnRepository $clientSnRepository, ChartBuilderInterface $chartBuilder, DeviceFamilyRepository $deviceFamilyRepository): Response
    {

        $username = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(array('username' => $username));
        $email = $user->getEmail();
        $clientIdentified = $clientRepository->findBy(array('email' => $email));
        $clientToSn = $clientSnRepository->findBy(array('client' => $email));
        $snArray = array();
        
        /*
        foreach ($clientToSn as $sn) {
            $device = $deviceRepository->findOneBy(array('sn' => $sn->getSn()));
            $snArray[] = $device;
        }
        */
        
        //$deviceConnected_array = $this->getDeviceServer($deviceServerRepository); //number of devices connected by day
        $deviceCreated_array = $this->getDeviceCreated($deviceRepository); // number of devices created by day
        $deviceCount_array = $this->getDeviceCount($deviceFamilyRepository);
        
        // TODO
        //$firstChart = $this->getChart($chartBuilder, array_keys($deviceConnected_array), "Devices connected", array_values($deviceConnected_array), "Devices connected per week", Chart::TYPE_LINE);
        $secondChart = $this->getChart($chartBuilder, array_keys($deviceCreated_array), "Devices created", array_values($deviceCreated_array), "Devices created per week", Chart::TYPE_LINE);
        $thirdChart = $this->getChart($chartBuilder, array_keys($deviceCount_array), 'label', array_values($deviceCount_array), 'Number of devices per type', Chart::TYPE_DOUGHNUT);
        
        /*
        if (($clientIdentified = $clientRepository->findBy(array('email' => $email)))!=false) {
            
            foreach ($clientIdentified as $client) {
                $clientSn = $client->getSerialNumber();
                $sn = $snRepository->findOneBy(array('SN' => $clientSn));
                $device = $deviceRepository->findOneBy(array('sn' => $clientSn));
                array_push($clientSnArray, $sn);
                array_push($clientDeviceArray, $device);
                // make an array with sn as key, sn object as value, device object as value
                // add clientSn to clientSnArray
            }
            
        }
        */

        $form = $this->createForm(ClientRegistrationType::class);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            //$clientSn = new ClientSn();
            $email = $form->get('email')->getData();
            $client->setEmail($email);

            //$client->setSerialNumber($form->get('serial_number')->getData());
            //$clientSn->setClient($email);
            //$clientSn->setSn($form->get('serial_number')->getData());

            $entityManager->persist($client);
            //$entityManager->persist($clientSn);
            $entityManager->flush();
            //$user = $userRepository->findOneBy(array('email' => $email));
            return $this->redirectToRoute('home');
        }
        
        return $this->render('main/index.html.twig', [
            'clientRegistration' => $form->createView(),
            'clientIdentified' => $clientIdentified,
            //'clientToSn' => $clientToSn,
            /*
            'deviceConnectedArray'=>$deviceConnected_array,
            'deviceCreatedArray'=>$deviceCreated_array,
            'snArray'=>$snArray,
            */
            // TODO
            //'firstChart'=>$firstChart,
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
    /**
     * @Route("/devicesConnected/", name="get_devices_connected")
     */
    function getDeviceServer(DeviceServerRepository $deviceServerRepository) {
        //$date_array = $this->getDate();
        $date_array = ["2023-09-27 11:33:09", "2023-09-27 10:55:24"];
        foreach ($date_array as $date) {
            //print_r($date);
            $allDevices = $deviceServerRepository->findByDate($date);
            print_r($allDevices);
            $deviceCount_array[$date] = count($allDevices);
        }
        return ($deviceCount_array);
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

    /**
     * getChart
     * @param ChartBuilderInterface $chartBuilder
     * @param array $labels - array of legends
     * @param string $label
     * @param array $dataArray - array of values
     * @param string $text
     * @return Chart
     */
    function getChart(ChartBuilderInterface $chartBuilder, $labels, $label, $dataArray, $text, $chartType) {
        $backgroundArray = $this->backgroundArray;
        $borderArray = $this->borderArray;
        //Chart::TYPE_LINE
        $chart = $chartBuilder->createChart($chartType);
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
            ],
            'maintainAspectRatio' => false,
        ]);

        return $chart;
    }

    public function getDeviceCount(DeviceFamilyRepository $deviceFamilyRepository) {
        $devicesFamily = $deviceFamilyRepository->findAll();
        for ($i=0; $i < sizeof($devicesFamily); $i++) {
            $deviceCountArray[$devicesFamily[$i]->getName()] = count($devicesFamily[$i]->getDevices());
        }
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