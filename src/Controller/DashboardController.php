<?php
namespace App\Controller;

use App\Class\SearchVersion;
use App\Form\DashboardVersionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Routing\Annotation\Route;

use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\SoftwareRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
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
     * @Route("/{_locale<%app.supported_locales%>}/user/dashboard/", name="dashboard")
     */
    /*
    public function dashboard(DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, DeviceFamilyRepository $deviceFamilyRepository, ChartBuilderInterface $chartBuilder) {

        $deviceCount_array = $this->getDeviceCount($deviceFamilyRepository);
        $thirdChart = $this->getChart($chartBuilder, array_keys($deviceCount_array), 'label', array_values($deviceCount_array), 'Number of devices per type', Chart::TYPE_DOUGHNUT);

        $devicesFamily = $deviceFamilyRepository->findAll();
        foreach ($devicesFamily as $deviceFamily) {
            # getChartByFamily
            $softwares = $softwareRepository->findBy(
                array('deviceFamily'=>$deviceFamily),
                array('name' => 'DESC'));
            //$softwareArray[$deviceFamily->getName()] = $softwares;
            $familyChart = $this->getChart($chartBuilder, $softwares, 'label', )
        }
        return $this->render('dashboard.html.twig', [
            'thirdChart'=>$thirdChart,
        ]);
    }
    */
    
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

        //TODO find devices updated by version
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
        //$devices = $deviceRepository->findDeviceByCriteria($version=$version, null, $family=$deviceFamilyId, null);
        $devices = $deviceRepository->findBy(array('version' => $version, 'deviceFamily' => $deviceFamilyId));
        return new Response(count($devices));
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

}