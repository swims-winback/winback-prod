<?php
namespace App\Controller;

use App\Class\SearchData;
use App\Entity\Device;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Type\DeviceType;
use App\Form\DeviceEditType;
use App\Form\DeviceVersionType;
use App\Form\DeviceCheckType;
use App\Form\SearchDeviceType;

use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\SoftwareRepository;

use Psr\Log\LoggerInterface;

class DeviceController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/device/", name="device")
     */
    public function index(DeviceRepository $deviceRepository, Request $request, SoftwareRepository $softwareRepository, ManagerRegistry $doctrine, LoggerInterface $logger)  
    {
        $data = new SearchData();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchDeviceType::class, $data);
        $form->handleRequest($request);
        $devices = $deviceRepository->findSearch($data);
        if ($devices->getItems() == null) {
            $this->addFlash(
                'error', 'Device(s) not found, please try again !'
            );
            return $this->redirectToRoute('device');
        }
        // Check-all form 
        $checkform = $this->createForm(DeviceCheckType::class);
        // input text version form
        $versionform = $this->editDeviceVersion($request, $deviceRepository, $softwareRepository, $doctrine, $logger);
        //TODO for each device check if file exists, else leave path blank
        $download_link = true;
        
        return $this->render('device.html.twig', [
            'devices' => $devices,
            'form' => $form->createView(),
            'checkform' => $checkform->createView(),
            'versionform' => $versionform,
            'ressource_path' => $_ENV["RESSOURCE_PATH"],
            'download_link' => $download_link
        ]);

    }
    
    // TODO Is it used ?
    //@Route("/{_locale<%app.supported_locales%>}/connect/{id}", name="connect")
    /*
    public function connect(Device $device, DeviceRepository $deviceRepository)  
    {
        //$device = $deviceRepository->findDeviceById($id);
        return $this->render('device/_acces.html.twig', [
            'device'=> $device,
        ]);
    }
    */

    /**
     * return versionformView to be called in index function
     */
    public function editDeviceVersion(Request $request, DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $user = $this->getUser();
        $devices = $deviceRepository->findAll();
        $versionform = $this->createForm(DeviceVersionType::class);
        $versionform->handleRequest($request);

        if($versionform->isSubmitted() && $versionform->isValid()) {
            foreach ($devices as $device) {
                $version_input = $versionform->get('versionUpload')->getData();
                //$category = $device->getDeviceFamily();
                //$version_software = $softwareRepository->findSoftwareByVersion($version_input, $category->getId());
                if($device->getSelected() ) {
                    $category = $device->getDeviceFamily();
                    //$version_software = $softwareRepository->findSoftwareByVersion($version_input, $category->getId());
                    $version_software = $softwareRepository->findOneBy(array('version'=>$version_input, 'deviceFamily'=>$category->getId()));
                    //var_dump($category->getId());
                    if ($version_software or $version_input == 0) {
                        $logger->info($user." has updated ".$device->getSn()." version from ".$device->getVersionUpload()." to ".$version_input);
                        $device->setVersionUpload($version_input);
                        
                        $this->addFlash(
                            'infoDevice', 'Device '.$device->getSn().' updated !'
                        );
                        
                    }
                    else {
                        $this->addFlash(
                            'error', 'Software '.$version_input.' not found, please try again !'
                        );
                    }
                    $device->setSelected(false);
                    $em = $doctrine->getManager();
                    $em->flush();
                }
            }
        }
        $versionformView = $versionform->createView();
        return $versionformView;
    }

    /**
     * @Route("/addDeviceVersion/{version}/{id}", name="add_version")
     * function called in js when updateVersion form is triggered
     */
    public function addDeviceVersion(Request $request, DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, ManagerRegistry $doctrine, LoggerInterface $logger, string $version, int $id)
    {
        $user = $this->getUser();
        //$devices = $deviceRepository->findAll();
        $device = $deviceRepository->findOneBy(array("id"=>$id));
        $category = $device->getDeviceFamily();
        $version_software = $softwareRepository->findOneBy(array('version'=>$version, 'deviceFamily'=>$category->getId()));
        if ($version_software or $version == 0) {
            $logger->info($user." has updated ".$device->getSn()." version from ".$device->getVersionUpload()." to ".$version);
            $device->setVersionUpload($version);
            
            $this->addFlash(
                'infoDevice', 'Device '.$device->getSn().' updated !'
            );
            $em = $doctrine->getManager();
            $em->persist($device);
            $em->flush();
        }
        else {
            $this->addFlash(
                'error', 'Software '.$version.' not found, please try again !'
            );
        }
        $device->setSelected(false);
        return $this->redirectToRoute('device');
        //return new Response("true");
        
    }

    /**
     * @Route("/forced/{id}/{select_bool}", name="forced")
     */
    public function forced(Device $device, DeviceRepository $deviceRepository, ManagerRegistry $doctrine, LoggerInterface $logger, int $id, $select_bool=false)
    {
        $user = $this->getUser();
        $logger->info($user." has forced ".$device->getSn());
        $device->setForced(($select_bool==0)?0:1);
        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();
        return $this->redirectToRoute('device');
    }

    /**
     * @Route("/isactive/{id}", name="isactive")
     */
    public function isActive(Device $device)
    {
        return new Response($device->getIsActive());
    }

    /**
     * @Route("/download/{id}", name="download")
     */
    public function download(Device $device)
    {
        return new Response($device->getDownload());
    }

    /**
     * @Route("/selected/{id}/{select_bool}", name="selected")
     */
    public function selected(Device $device, ManagerRegistry $doctrine, $select_bool)
    {
        print_r($select_bool);
        $device->setSelected(($select_bool==0)?0:1);
        //$device->setSelected($select_bool);
        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        return $this->redirectToRoute('device');
    }  

    /**
    * @Route("/updated/{id}/{version}/", name="updated")
    * Update version in modal
    */
    public function updated(Request $request, Device $device, ManagerRegistry $doctrine, SoftwareRepository $softwareRepository, LoggerInterface $logger, $version)
    {
        $user = $this->getUser();
        $category = $device->getDeviceFamily();
        $version_software = $softwareRepository->findSoftwareByVersion($version, $category->getId());
        if ($version_software or $version == 0) {
            $logger->info($user." has updated ".$device->getSn()." version from ".$device->getVersionUpload()." to ".$version);
            $device->setVersionUpload($version);
            $em = $doctrine->getManager();
            $em->persist($device);
            $em->flush();
        }
        else {
            $this->addFlash(
                'error', 'Software '.$version.' not found, please try again !'
            );
        }  
        //return new Response("true");
        return $this->redirectToRoute('device');
    }

    /**
     * @Route("/addComment/{id}/{comment}", name="add_comment")
     */
    public function addComment(ManagerRegistry $doctrine, DeviceRepository $deviceRepository, $id, $comment, LoggerInterface $logger) {
        $user = $this->getUser();
        $device = $deviceRepository->findOneBy(array('id' => $id));
        if ($comment == "null") {
            $comment = "";
            $logger->info($user." has deleted comment.");
            $this->addFlash('infoDevice', 'Comment deleted with success !');
        }
        else {
            $logger->info($user." has added comment ".$comment);
            $this->addFlash('infoDevice', 'Comment '.$comment.' added with success to '.$device->getSn().'!');
        }
        $device->setComment($comment);

        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        //return new Response("true");
        return $this->redirectToRoute('device');
    }

    /**
     * @Route("/addServerId/{id}/{serverId}", name="add_server_id")
     */
    public function addServerId(ManagerRegistry $doctrine, DeviceRepository $deviceRepository, $id, $serverId, LoggerInterface $logger) {
        $user = $this->getUser();
        $device = $deviceRepository->findOneBy(array('id' => $id));
        $logger->info($user." has changed serverId to ".$serverId." for ".$device->getSn());
        $this->addFlash('infoDevice', 'ServerId '.$serverId.' changed for '.$device->getSn().'!');
        $device->setServerId($serverId);
        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();
        return $this->redirectToRoute('device');
    }
}

