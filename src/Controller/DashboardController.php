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
/*
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
*/
class DashboardController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/dashboard/", name="dashboard")
     */
    public function dashboard(DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, DeviceFamilyRepository $deviceFamilyRepository) {
        //$data = new SearchVersion();
        $devicesFamily = $deviceFamilyRepository->findAll();
        $devices = $deviceRepository->findAll();
        $countDevices = count($devices);
        $deviceArray = array();
        //$form = $this->createForm(DashboardVersionType::class, $data);
        
        foreach ($devicesFamily as $deviceFamily) {
            //$softwares = $deviceFamily->getSoftware();
            //$softwares = $softwareRepository->findByDeviceFamily($deviceFamily);
            $softwares = $softwareRepository->findBy(
                array('deviceFamily'=>$deviceFamily),
                array('name' => 'DESC'));
            $softwareArray[$deviceFamily->getName()] = $softwares;
            //$deviceArray[$deviceFamily->getName()] = count($devices);
        }

        /* TEST */
        /*
        $labels = [];
        $data = [];

        foreach ($devicesFamily as $deviceFamily) {
            $labels[] = $deviceFamily->getName();
            $data[] = count($deviceFamily->getDevices());
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels'=> $labels,
            'datasets'=> [
                [
                    'label'=> 'Total Devices 2',
                    'backgroundColor'=> [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                      ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    'data'=>$data,
                ],
            ],
        ]);

        $chart->setOptions([]);
        */
        /* TEST */
        //TODO find devices updated by version
        return $this->render('dashboard.html.twig', [
            'devicesFamily'=> $devicesFamily,
            'countDevices'=> $countDevices,
            //'deviceArrayVersion'=> $deviceArrayVersion,
            'deviceArray'=> $deviceArray,
            //'deviceArrayVersion'=> $deviceArrayVersion,
            //'result'=> $result
            'softwareArray'=> $softwareArray,
            //'chart'=>$chart,
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

}