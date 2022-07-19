<?php
namespace App\Controller;

use App\Entity\Device;
use App\Form\SearchDeviceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\Type\DeviceType;
use App\Form\DeviceEditType;
use App\Repository\DeviceFamilyRepository;
use App\Repository\DeviceRepository;
use App\Repository\SoftwareRepository;
use App\Server\DbRequest;
use App\Services\FileUploader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

use function PHPUnit\Framework\throwException;

use App\Server\TCPServer;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DeviceController extends AbstractController
{
    /**
     * @Route("/admin/device/", name="device")
     */
    public function index(DeviceRepository $deviceRepository, DeviceFamilyRepository $deviceFamilyRepository, SoftwareRepository $softwareRepository, Request $request, ManagerRegistry $doctrine, DbRequest $dbrequest): Response
    {
        $devices = $deviceRepository->findAll();
        $families = $deviceFamilyRepository->findAll();
        //$devices = $deviceRepository->findBy(['sn' => 'desc'], 5);
        //$softwares = $softwareRepository->findAll();

        $searchform = $this->createForm(SearchDeviceType::class);

        $search = $searchform->handleRequest($request);
        
        if($searchform->isSubmitted() && $searchform->isValid()) {
            $devices = $deviceRepository->search(
                $value = $search->get('value')->getData(), 
                $search->get('max_result')->getData(),
                $family = $search->get('category')->getData(),
                //$search->get('version')->getData(),
                $version = $search->get('version')->getData(),
                $versionUpload = $search->get('versionUpload')->getData(),
                //$forced = $search->get('forced')->getData(),
                //var_dump($version),
            );
            if ($devices == null) {
                $this->addFlash(
                    'error', 'Device '.$value.' not found, please try again !'
                );
                return $this->redirectToRoute('device');
            }
        
        }

        //TODO old search form in notes


        // input text version form
        $form = $this->createFormBuilder()
        ->add('versionInput', TextType::class, [
            'label' => false
        ])
        ->add('Save', SubmitType::class, [
            'attr' => [
                'class' => 'btn rounded-1 bg-orange',
            ],
            //'label' => false,
        ])
        ->getForm();
        
        // Check-all form 
        $checkform = $this->createFormBuilder()
        ->add('check', CheckboxType::class, [
            'attr' => [
                'name' => 'checkbox',
                //'id' => 'checkbox_0',
                'type' => 'checkbox'
            ],
            'label' => false
        ])
        ->getForm();
        

        //$devices = $deviceRepository->findAll();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //for ($i=1; $i <= sizeof($devices); $i++) {
            foreach ($devices as $device) {   
                //$deviceId = $i;
                //print_r($deviceId);
                //$device = $deviceRepository->findDeviceById($deviceId);
                $version_input = $form->get('versionInput')->getData();
                $category = $device->getDeviceFamily();
                $version_software = $softwareRepository->findSoftwareByVersion($version_input, $category);
                //print_r($deviceId);
                //$device->setSelected(($device->getSelected())?false:true);
                if($device->getSelected()) {
                    $device->setVersionUpload($version_input);
                    $device->setSelected(false);
                    $em = $doctrine->getManager();
                    $em->flush();
                    /*
                    if ($version_software) {

                    }
                    else {
                        //throw new NotFoundResourceException("Software does not exist");
                        $this->addFlash('error', 'Software does not exist');
                    }
                    */
                }
                
                else {
                    //$device->setSelected(true);
                }
                
                
            }
            return $this->redirectToRoute('device');
        }


        return $this->render('device.html.twig', [
            'devices' => $devices,
            'searchform' => $searchform->createView(),
            //'versionform' => $versionForm->createView(),
            'families' => $families,
            'form' => $form->createView(),
            'checkform' => $checkform->createView(),
            //'checkitemform' => $checkitemform->createView()

            //'forms' => $forms,
            //'versionforms' => $versionforms,
        ]);
    }
    /**
     * @Route("/admin/device/add", name="device_add")
    */
    /*
    public function addDevice(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader, DeviceFamilyRepository $deviceFamilyRepository): Response
    {
        $device = new Device;

        $form = $this->createForm(DeviceType::class, $device);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $familyName = $form->get('deviceFamily')->getData();
            
            $family = $deviceFamilyRepository->findFamilyByName($familyName);
            $familyType = $family->getNumberId();
            $device->setType($familyType);

            $version = $form->get('version')->getData();
            $device->setVersionUpload($version);
            ///*
            $deviceFile = $form->get('file')->getData();

            if ($deviceFile) {
                $originalFilename = $fileUploader->upload($deviceFile, 'devices/');
                //$deviceName = $originalFilename;

                $device->setDeviceFile($originalFilename);
                $fileFolder = __DIR__.'/../../public/uploads/devices/';
                $spreadsheet = IOFactory::load($fileFolder . $originalFilename); // Here we are able to read from the excel file 
                $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line 
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
                $sheetDataSlice = array_slice($sheetData, 0, 10);
                //dd(array_slice($sheetData, 0, 10));

                foreach ($sheetDataSlice as $Row)
                {

                    $familyName = $Row['A'];
                    $sn = $Row['B'];
                    $family = $deviceFamilyRepository->findFamilyByName($familyName);
                    $device->setSn($sn);
                    $device->setDeviceFamily($family);

                    $em = $doctrine->getManager();
                    $device = $form->getData();
                    $em->persist($device);
                    //$em->flush();
                }

            }
            //TODO Comment here

              
            $em = $doctrine->getManager();
            $device = $form->getData();
            $em->persist($device);
            $em->flush();
            

            return $this->redirectToRoute('device');
        }

        return $this->render('device/add.html.twig', [
            'form' => $form->createView(),
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
        $device->setType($familyType);

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
     * @Route("/admin/device/addmultiple", name="device_add_multiple")
    */
    /*
    public function addMultipleDevice(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $device = new Device;

        $form = $this->createForm(DeviceType::class, $device);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $deviceFile = $form->get('file')->getData();

            if ($deviceFile) {
                $originalFilename = $fileUploader->upload($deviceFile, 'devices/');
                //$deviceName = $originalFilename;

                $device->setDeviceFile($originalFilename);
                $fileFolder = __DIR__.'/../../public/uploads/devices/';
                $spreadsheet = IOFactory::load($fileFolder . $originalFilename); // Here we are able to read from the excel file 
                $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line 
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
                dd(array_slice($sheetData, 0, 10));
            }

            $em = $doctrine->getManager();
            $device = $form->getData();
            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('device');
        }
        return $this->renderForm('device/add.html.twig', [
            'form' => $form,
        ]);
    }
    */


    /**
     * @Route("/admin/device/edit/{id}", name="device_edit")
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

    /**
     * @Route("/admin/device/edit/multiple{id}", name="device_edit_multiple")
    */
    public function editMultipleDevice(Request $request, ManagerRegistry $doctrine, Device $device, DeviceRepository $deviceRepository): Response
    {
        //$em = $doctrine->getManager();
        $devices = $deviceRepository->findAll();
        //$form = $this->createForm(DeviceEditType::class, $device);
        $form = $this->createFormBuilder()
            ->add(
                'devices', CollectionType::class, [
                    'type' => DeviceEditType::class,
                    'allow_add' => false,
                    'allow_delete' => false,
                    'label' => false
                ]
            )
            ->add('save', 'submit', array('label' => 'Create'))
            ->getForm();
        $form->setData(array('devices' => $devices));
        $form->handleRequest($request);

        /*if($form->isSubmitted() && $form->isValid())
        {

            $em = $doctrine->getManager();
            $device = $form->getData();
            $em->persist($device);
            $em->flush();

            return $this->redirectToRoute('device');
        } */
        return $this->renderForm('device/editAll.html.twig', [
            array(
                'form' => $form->createView()
            )
            //'form' => $form,
            //'device' => $device
        ]);
    }

    /**
     * @Route("/admin/device/delete/{id}", name="device_delete")
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
     * @Route("/admin/device/forced/{id}", name="forced")
     */
    public function forced(Device $device, ManagerRegistry $doctrine)
    {
        $device->setForced(($device->getForced())?false:true);

        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        //return new Response("true");
        return $this->redirectToRoute('device');
    }

    /**
     * @Route("/admin/device/selected/{id}", name="selected")
     */
    public function selected(Device $device, ManagerRegistry $doctrine)
    {
        $device->setSelected(($device->getSelected())?false:true);

        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        //return new Response("true");
        return $this->redirectToRoute('device');
    }

    


    /**
     * @Route("/admin/device/updated/{id}/{version}", name="updated")
     */
    public function updated(Device $device, ManagerRegistry $doctrine, $version)
    {
        //print_r($version);
        
        $device->setVersionUpload($version);

        $em = $doctrine->getManager();
        $em->persist($device);
        $em->flush();

        //return new Response("true");
        return $this->redirectToRoute('device');
        
    }
}

