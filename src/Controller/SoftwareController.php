<?php
namespace App\Controller;

use App\Entity\DeviceFamily;
use App\Entity\Software;
use App\Form\SearchSoftwareType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\SoftwareType;
use App\Repository\DeviceFamilyRepository;
use App\Repository\SoftwareRepository;
use App\Services\FileUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Finder\Finder;
use App\Server\DbRequest;
use Symfony\Component\Filesystem\Filesystem;

class SoftwareController extends AbstractController
{
    /**
     * @Route("/admin/software/", name="software")
     */
    /*     
    public function index(SoftwareRepository $softwareRepository, Request $request): Response
    {
        $softwares = $softwareRepository->findAll();

        $form = $this->createForm(SearchSoftwareType::class);

        
        $search = $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            // On recherche les annonces correspondant aux mots clefs
            $softwares = $softwareRepository->search($search->get('word')
            ->getData());
        }

        return $this->render('software.html.twig', [
            'softwares' => $softwares,
            'form' => $form->createView()
        ]);
    } */
    public function index(SoftwareRepository $softwareRepository, DeviceFamilyRepository $deviceFamilyRepository, Request $request, DbRequest $dbRequest, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {

        $software = null;
        $softwares = $softwareRepository->findAll();
        //$families = $deviceFamilyRepository->findAll();
        $families = $deviceFamilyRepository->findFamilyByNameAlpha();

        //$software = new Software();

        //$form = $this->createForm(SoftwareType::class, $software);

        $searchform = $this->createForm(SearchSoftwareType::class);
        
        $search = $searchform->handleRequest($request);
        
        if($searchform->isSubmitted() && $searchform->isValid()) {
            // On recherche les annonces correspondant aux mots clefs
            $softwares = $softwareRepository->search(
                /*   
                $search->get('word')->getData(), 
                $search->get('max_result')->getData(),
                */
                /*
                $search->get('value')->getData(),
                $software_name = $search->get('value')->getData(),
                $search->get('category')->getData(),
                $family_name = $search->get('category')->getData(),
                //var_dump($family_name->getName()),
                */
                //$value = $search->get('value')->getData(),
                //$version = $search->get('version')->getData(),
                $software_name = $search->get('value')->getData(),
                $search->get('category')->getData(),
                $family_name = $search->get('category')->getData(),
            );
            if ($softwares == null) {
                $this->addFlash(
                    'errorSoftware', 'Software '.$software_name.' not found, please try again !'
                );
                return $this->redirectToRoute('software');
            }
            
            /*
            if ($software_name != null) {
                //$families = $deviceFamilyRepository->findFamilyBySoftware($family_name->getSoftware());
                //var_dump($software_name);
                $software = $softwareRepository->findSoftwareByName($software_name);
                //var_dump($software->getFamily()->getName());
                //var_dump($software->getName());
                //var_dump($software);
                if ($software) {
                    //var_dump($software);
                    $families = $deviceFamilyRepository->findFamilyByNameAll($software->getFamily()->getName());
                    //return $this->redirectToRoute('software');
                }
                else {
                    $families = null;
                    $this->addFlash(
                        'errorSoftware', 'Software '.$software_name.' not found, please try again !'
                    );
                    return $this->redirectToRoute('software');
                    //throw new NotFoundHttpException('Software not found, please try again !');
                    
                }

            }
            */
            if ($family_name != null) {
                $families = $deviceFamilyRepository->findFamilyByNameAll($family_name->getName());
            }
            //return $this->redirectToRoute('software');
        }

        //$uploadform = $this->addSoftware($request, $dbRequest, $doctrine, $fileUploader, $deviceFamilyRepository);
        $this->addDirectorySoftware($dbRequest);

        return $this->render('software.html.twig', [
            'softwares' => $softwares,
            'software' => $software,
            //'form' => $form,
            'families' => $families,
            'searchform' => $searchform->createView(),
            //'uploaform' => $uploadform,
        ]);
    }

    function insertNewSoftware($name, $devType, $version, $date){
        //$req = "INSERT INTO ".SOFTWARE_TABLE." (".NAME.", ".FAMILY_TYPE.", ".SOFT_VERSION.", ".SOFT_CREATED_AT.") VALUES ('".$name.", ".$devType.", ".$version.", ".$date."')";
        $req = "INSERT IGNORE INTO ".SOFTWARE_TABLE." (".NAME.", ".FAMILY_TYPE.", ".SOFT_VERSION.", ".SOFT_CREATED_AT.") VALUES ('".$name."', '".$devType."', '".$version."', '".$date."')"; //ON DUPLICATE KEY UPDATE ".UPDATED_AT."= '".date("Y-m-d | H:i:s")."'";
        //$req = "INSERT INTO ".SOFTWARE_TABLE." (".NAME.", ".FAMILY_TYPE.", ".SOFT_VERSION.", ".SOFT_CREATED_AT.") VALUES ('".$name."', '".$devType."', '".$version."', '".$date."') ON DUPLICATE KEY UPDATE ".UPDATED_AT."= '".date("Y-m-d | H:i:s")."'";
        //$req = "INSERT INTO ".SOFTWARE_TABLE." (".NAME.", ".FAMILY_TYPE.", ".SOFT_VERSION.", ".SOFT_CREATED_AT.") VALUES ('".$name."', '".$devType."', '".$version."', '".$date."')"; //ON DUPLICATE KEY UPDATE ".UPDATED_AT."= '".date("Y-m-d | H:i:s")."'";
        return $req;
    }
	
    function initSoftwareInDB($name, $devType, $version, $date, DbRequest $request){
        $req = $this->insertNewSoftware($name, $devType, $version, $date);
        $res = $request->sendRq($req);
        
        return false;
    }

    function updateNewSoftware($name, $devType, $version, $date){
        $req = "INSERT INTO ".SOFTWARE_TABLE." (".NAME.", ".FAMILY_TYPE.", ".SOFT_VERSION.", ".SOFT_CREATED_AT.") VALUES ('".$name."', '".$devType."', '".$version."', '".$date."') ON DUPLICATE KEY UPDATE ".SOFT_VERSION."= '".$version."',".UPDATED_AT."= '".date("Y-m-d | H:i:s")."'";
        return $req;
    }
	
    function updateSoftwareInDB($name, $devType, $version, $date, DbRequest $request){
        $req = $this->updateNewSoftware($name, $devType, $version, $date);
        $res = $request->sendRq($req);
        
        return false;
    }

    // Check Directory is copied in db
    public function addDirectorySoftware(DbRequest $request)
    {
        // for each device type in device type array
        foreach (deviceType as $key => $deviceType) {
            if (file_exists(PACK_PATH.$deviceType)) {
                $this->aVersion[$deviceType] = array_diff(scandir(PACK_PATH.$deviceType), array('.'));
                array_shift($this->aVersion[$deviceType]);
                if (!file_exists(UPLOAD_PATH."softwares/".$deviceType)) {
                    mkdir(UPLOAD_PATH."softwares/".$deviceType);
                }
                //array_shift($this->aVersion[$device]);
                //if record not in db, add record
                // for each file in device type folder, create record in db if not exists
                foreach ($this->aVersion[$deviceType] as $key => $file) {
                    //var_dump($file);
                    if (str_ends_with($file, ".bin")) {
                        $fileArray = explode("v", $file);
                        $version = substr($fileArray[1], -11, 7);
                        $deviceTypeId = $request->getDeviceType(deviceId[$deviceType], ID);
                        //$this->request->initSoftwareInDB($name=$this->aVersion[$deviceType][0], $devType=$deviceType, $version="0", $date=date("Y-m-d | H:i:s"));
                        if (!file_exists(UPLOAD_PATH."softwares/".$deviceType.$file)) {
                            copy(PACK_PATH.$deviceType.$file, UPLOAD_PATH."softwares/".$deviceType.$file);
                        }
                        if (!file_exists(PACK_ARCH_PATH.$deviceType.$file)) {
                            copy(PACK_PATH.$deviceType.$file, PACK_ARCH_PATH.$deviceType.$file);
                        }
                        $this->initSoftwareInDB($name=$file, $devType=$deviceTypeId, $version, $date=date("Y-m-d | H:i:s"), $request);
                    }
                }
            }
        }
    }

    /**
     * @Route("/admin/software/add", name="software_add")
     * Upload new software with form
    */
    public function addSoftware(Request $request, DbRequest $dbRequest, ManagerRegistry $doctrine, FileUploader $fileUploader, DeviceFamilyRepository $deviceFamilyRepository): Response
    {
        $software = new Software;
        //$family = new DeviceFamily;

        $form = $this->createForm(SoftwareType::class, $software);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $softwareFile = $form->get('file')->getData();
            echo($form->get('file')->getData()->getClientOriginalName());
            if ($softwareFile) {
                $fileName = $form->get('file')->getData()->getClientOriginalName();
                //$softwareNumber = substr($fileName, 7, 2);
                $deviceType = substr($fileName, 7, 2);
                //$deviceTypeId = $dbRequest->getDeviceType(deviceTypeId[$deviceType], ID);
                //var_dump($deviceType);
                $family = $deviceFamilyRepository->findFamilyByNumberId($deviceType);
                

                //var_dump($softwareFile);
                //$originalFilename = $fileUploader->upload($softwareFile, 'softwares/');
                $originalFilename = $fileUploader->upload($softwareFile, "package/".deviceTypeArray[$deviceType]);
                //$originalFilename = $fileUploader->upload($fileName, "softwares/".deviceTypeArray[$deviceType]);
                $softwareVersion = substr($fileName, -11, 7);
                //var_dump($softwareVersion);
                //$pattern = '/^0*/i';
                $softwareName = $originalFilename;

                $pattern2 = '/-/i';
                //$pattern3 = '((?:0)*(\d+)).((?:0)*(\d+))';
                //$softwareVersionModified = preg_replace($pattern, '', $softwareVersion);
                $softwareVersionModified = preg_replace($pattern2, '.', $softwareVersion);
                //$softwareVersionModified2 = preg_replace($pattern3, '\1', $softwareVersionModified);
                $pattern3 = "/^0{1,2}/";
                $pattern4 = "/\.0{1,2}/";
                $softwareVersionModified2 = preg_replace($pattern3, '', $softwareVersionModified);
                $softwareVersionModified3 = preg_replace($pattern4, '.', $softwareVersionModified2);
                //print_r($softwareVersionModified2);
                //$softwareNumber = substr($originalFilename, 7, 2);

                //$familyRepository = $doctrine->getRepository(DeviceFamily::class);

                //$family = $familyRepository->find($softwareNumber);
                //$family = $deviceFamilyRepository->findFamilyByNumberId($softwareNumber);
                // on veut comparer le nombre software avec le software family où numberId = software number
                //$softwareFamily = $family->getnumberId($softwareNumber);
                //$family->setNumberId($softwareNumber);
                $software->setFamily($family);
                $software->setName($fileName);
                $software->setSoftwareFile($fileName);
                $software->setVersion($softwareVersionModified3);

                $this->updateSoftwareInDB($name=$fileName, $devType=deviceTypeId[$deviceType], $version=$softwareVersionModified3, $date=date("Y-m-d | H:i:s"), $dbRequest);
            }

            /*
            $em = $doctrine->getManager();
            $software = $form->getData();
            //$em->persist($family);
            $em->persist($software);
            $em->flush();
            */
            //$this->addFlash('info', 'Software append with success !');
            $this->addFlash('infoSoftware', 'Software '.$fileName.' added with success !');
            return $this->redirectToRoute('software');
            //return true;
        }
        return $this->renderForm('software/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/software/edit/{id}", name="software_edit")
    */
    public function editSoftware(Request $request, ManagerRegistry $doctrine, Software $software): Response
    {
        
        $form = $this->createForm(SoftwareType::class, $software);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $em = $doctrine->getManager();
            $software = $form->getData();
            $em->persist($software);
            $em->flush();

            return $this->redirectToRoute('software');
        }

        return $this->renderForm('software/add.html.twig', [
            'form' => $form,
            'software' => $software
        ]);
    }

    /**
     * @Route("/admin/software/delete/{id}", name="software_delete")
    */    
    public function deleteSoftware(Software $software, ManagerRegistry $doctrine)
    {
        $filesystem = new Filesystem();
        // récupère le nom du software
        $name = $software->getName();
        $deviceType = $software->getFamily()->getName();
        //var_dump(deviceIdType[$deviceType]);
        //var_dump($deviceType);
        // supprime le fichier dans la bdd
        //unlink($this->getParameter('softwares_directory').'/'.$deviceType."/".$name);
        //unlink('./uploads/softwares/'.$deviceType."/".$name);
        /*
        if ($filesystem->exists(['softwares_directory'.'/'.$deviceType."/".$name])) {
            $filesystem->remove(['softwares_directory'.'/'.$deviceType."/".$name]);
        }
        */
        //$filesystem->remove(['softwares_directory'.'/'.$deviceType."/".$name]);
        //$filesystem->remove(['packages_directory'.'/'.$deviceType."/".$name]);
        //var_dump($name);
        /*
        if (file_exists($this->getParameter('softwares_directory').'/'.$deviceType."/".$name)) {
            var_dump($this->getParameter('softwares_directory').'/'.$deviceType."/".$name);
        }
        */
        if (file_exists($this->getParameter('softwares_directory').'/'.$deviceType."/".$name)) {
            unlink($this->getParameter('softwares_directory').'/'.$deviceType."/".$name);
        }
        if (file_exists($this->getParameter('archives_directory').'/'.$deviceType."/".$name)) {
            unlink($this->getParameter('archives_directory').'/'.$deviceType."/".$name);
        }
        //unlink(PACK_PATH.$deviceType.$name);

        $em = $doctrine->getManager();
        $em->remove($software);
        $em->flush();
        
        $this->addFlash('message', 'Software '.$name.' deleted with success !');
        return $this->redirectToRoute('software');
    }
}