<?php
namespace App\Controller;

use App\Class\SearchData;
use App\Entity\Device;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Type\DeviceType;
use App\Form\DeviceEditType;
//use App\Form\DevicePageType;
use App\Form\DeviceVersionType;
use App\Form\DeviceCheckType;
use App\Form\DeviceCommentType;
use App\Form\SearchDeviceType;

use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\SoftwareRepository;
use App\Services\FileUploader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

use function PHPUnit\Framework\throwException;

use App\Server\TCPServer;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

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
        /*
        if ($devices == null) {
            $this->addFlash(
                'error', 'Device not found, please try again !'
            );
            return $this->redirectToRoute('device');
        }
        */
        // Check-all form 
        $checkform = $this->createForm(DeviceCheckType::class);
        // input text version form
        $versionform = $this->editDeviceVersion($request, $deviceRepository, $softwareRepository, $doctrine, $logger);
        return $this->render('device.html.twig', [
            'devices' => $devices,
            'form' => $form->createView(),
            'checkform' => $checkform->createView(),
            'versionform' => $versionform,
        ]);

    }
    /**
     * @Route("/{_locale<%app.supported_locales%>}/connect/{id}", name="connect")
     */
    /*
    public function connect(Device $device, DeviceRepository $deviceRepository)  
    {
        //$device = $deviceRepository->findDeviceById($id);
        return $this->render('device/_acces.html.twig', [
            'device'=> $device,
        ]);

    }
    */

    public function addDevice(ManagerRegistry $doctrine, DeviceFamilyRepository $deviceFamilyRepository, $familyName, $version)
    {
        $device = new Device;

        //$form = $this->createForm(DeviceType::class, $device);

        //$form->handleRequest($request);

        //if($form->isSubmitted() && $form->isValid())
        //{
            
        //$familyName;
        
        $family = $deviceFamilyRepository->findFamilyByName($familyName);
        $familyType = $family->getNumberId();

        //$version = $form->get('version')->getData();
        //$version;
        $device->setVersionUpload($version);
        ///*
        //$deviceFile = $form->get('file')->getData();

        //TODO Comment here

            
        $em = $doctrine->getManager();
        //$device = $form->getData();
        $em->persist($device);
        $em->flush();
        

        //return $this->redirectToRoute('device');
        return true;
        /*
        return $this->render('device/add.html.twig', [
            'form' => $form->createView(),
        ]);
        */
    }


    /**
     * @Route("/{_locale<%app.supported_locales%>}/edit/{id}", name="device_edit")
    */
    public function editDevice(Request $request, ManagerRegistry $doctrine, Device $device): Response
    {
        $editform = $this->createForm(DeviceEditType::class, $device);
        $editform->handleRequest($request);
        if($editform->isSubmitted() && $editform->isValid())
        {

            $em = $doctrine->getManager();
            $device = $editform->getData();
            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('device');
        }
        return $this->renderForm('device.html.twig', [
            'editform' => $editform,
            'device' => $device
        ]);
    }

    public function editDeviceVersion(Request $request, DeviceRepository $deviceRepository, SoftwareRepository $softwareRepository, ManagerRegistry $doctrine, LoggerInterface $logger)
    {
        $user = $this->getUser();
        $devices = $deviceRepository->findAll();
        $versionform = $this->createForm(DeviceVersionType::class);
        $versionform->handleRequest($request);

        if($versionform->isSubmitted() && $versionform->isValid()) {
            foreach ($devices as $device) {
                $version_input = $versionform->get('versionUpload')->getData();
                $category = $device->getDeviceFamily();
                $version_software = $softwareRepository->findSoftwareByVersion($version_input, $category->getId());
                if($device->getSelected() ) {
                    if ($version_software  or $version_input == 0) {
                        //$user = $this->getUser();
                        $logger->info($user." has updated ".$device->getSn()." version from ".$device->getVersionUpload()." to ".$version_input);
                        $device->setVersionUpload($version_input);
                        //TODO ici il faudrait envoyer un "socket_close()" au server et fermer la bonne socket
                        
                        /*
                        $this->addFlash(
                            'infoDevice', 'Device '.$device->getSn().' updated !'
                        );
                        */
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
                
                else {
                    /*
                    $device->setSelected(false);
                    $em = $doctrine->getManager();
                    $em->flush();
                    */
                }
                
                
            }
            //TODO A vérifier si ça affiche bien le nombre de dévices ET sélectionnés ET updatés
            /*
            $this->addFlash(
                'infoDevice', sizeof($deviceRepository->findAllSelected()).' devices updated !'
            );
            */
            //return $this->redirectToRoute('device');
        }
        return $versionform->createView();
    }


    /**
     * @Route("/delete/{id}", name="device_delete")
    */    
    public function deleteDevice(Device $device, ManagerRegistry $doctrine)
    {

        $em = $doctrine->getManager();
        $em->remove($device);
        $em->flush();

        //$this->addFlash('message', 'Device deleted with success !');
        return $this->redirectToRoute('device');
    }

    /**
     * @Route("/forced/{id}/{select_bool}", name="forced")
     */
    public function forced(Device $device, ManagerRegistry $doctrine, $select_bool)
    {
        $device->setForced(($select_bool==0)?0:1);
        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        //return new Response("true");
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
     * @Route("/selected/", name="selectedAll")
     */
    public function selectedAll(DeviceRepository $deviceRepo)
    {
        return new Response(sizeof($deviceRepo->findAllSelected()));
    }   

    /**
    * @Route("/updated/{id}/{version}/", name="updated")
    */
    public function updated(Request $request, Device $device, ManagerRegistry $doctrine, SoftwareRepository $softwareRepository, LoggerInterface $logger, $version)
    //public function updated(Request $request, Device $device, ManagerRegistry $doctrine, SoftwareRepository $softwareRepository, LoggerInterface $logger)
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
    
    public function updated_bool(Device $device, ManagerRegistry $doctrine, $version, $select_bool)
    {
        //print_r($version);
        if ($select_bool == true) {
            $device->setVersionUpload($version);

            $em = $doctrine->getManager();
            $em->persist($device);
            $em->flush();
    
            //return new Response("true");
            return $this->redirectToRoute('device');
        }

        
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
}

