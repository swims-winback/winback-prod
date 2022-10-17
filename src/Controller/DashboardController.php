<?php
namespace App\Controller;

use App\Class\SearchVersion;
use App\Form\DashboardVersionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Routing\Annotation\Route;

use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    /**
     * @Route("/user/dashboard/", name="dashboard")
     */
    public function dashboard(DeviceRepository $deviceRepository, DeviceFamilyRepository $deviceFamilyRepository) {
        //$data = new SearchVersion();
        $devicesFamily = $deviceFamilyRepository->findAll();
        $devices = $deviceRepository->findAll();
        $countDevices = count($devices);
        $deviceArray = array();
        //$form = $this->createForm(DashboardVersionType::class, $data);
        
        /*
        foreach ($devicesFamily as $deviceFamily) {
            $devices = $deviceRepository->findDeviceByCriteria($version=3.6, null, $family=$deviceFamily, null);
            //count($devices);
            //$deviceArray[`$deviceFamily`] = count($devices);
            //$deviceArray = array($deviceFamily->getName());
            $deviceArray[$deviceFamily->getName()] = count($devices);
        }
        */
        foreach ($devicesFamily as $deviceFamily) {
            //$devices = $deviceRepository->findDeviceByCriteria($version=3.6, null, $family=$deviceFamily, null);
            $softwares = $deviceFamily->getSoftware();
            $softwareArray[$deviceFamily->getName()] = $softwares;
            //$deviceArray[$deviceFamily->getName()] = count($devices);
        }

        //TODO find devices updated by version
        return $this->render('dashboard.html.twig', [
            'devicesFamily'=> $devicesFamily,
            'countDevices'=> $countDevices,
            //'deviceArrayVersion'=> $deviceArrayVersion,
            'deviceArray'=> $deviceArray,
            //'deviceArrayVersion'=> $deviceArrayVersion,
            //'result'=> $result
            'softwareArray'=> $softwareArray,
        ]);
    }

    /**
     * @Route("/version/{deviceFamily}/{version}", name="get_version")
     */
    public function getVersion($deviceFamily, $version, DeviceRepository $deviceRepository, DeviceFamilyRepository $deviceFamilyRepository)
    {
        $deviceFamilyId = $deviceFamilyRepository->findFamilyByName($deviceFamily)->getId();
        $devices = $deviceRepository->findDeviceByCriteria($version=$version, null, $family=$deviceFamilyId, null);
        return new Response(count($devices));
    }

}