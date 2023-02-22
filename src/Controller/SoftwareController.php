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
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class SoftwareController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/software/", name="software")
     */    
    public function index(SoftwareRepository $softwareRepository, DeviceFamilyRepository $deviceFamilyRepository, Request $request, DbRequest $dbRequest, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        /*
        $families = $deviceFamilyRepository->findBy(array(), array('name' => 'ASC'));
        $searchform = $this->createForm(SearchSoftwareType::class);
        $search = $searchform->handleRequest($request);
        foreach ($families as $fam) {
            $softwares = $fam->getSoftwares();
            $soft = $softwares->get(0);
            echo ($soft);
            //$softwares = $deviceFamilyRepository->findby(array('name' => 'ASC'), array('softwares' => 'ASC'));
            
            $filteredCollection = $softwares->filter(function($element) {
                return $element = "WLE256_11_2_v003.007.bin";
            });
            
            //print_r($filteredCollection);
        }
        */

        // je veux créer un families array avec les softwares sélectionnés en value

        
        $software = null;
        $softwares = $softwareRepository->findAll();
        //$softwares = $softwareRepository->findBy(array(), array('name' => 'DESC'));
        $families = $deviceFamilyRepository->findBy(array(), array('name' => 'ASC'));
        //$families = $deviceFamilyRepository->findBy(array(), array('name' => 'ASC'));

        $searchform = $this->createForm(SearchSoftwareType::class);
        $search = $searchform->handleRequest($request);
        
        if($searchform->isSubmitted() && $searchform->isValid()) {
            // On recherche les annonces correspondant aux mots clefs
            
            $software_name = $search->get('value')->getData();
            $family_name = $search->get('category')->getData();
            
            //echo ($software_name);
            
            $softwares = $softwareRepository->search(
                $software_name,
                $family_name,
            );
            
            //$families = $deviceFamilyRepository->findBy(array('softwares' => $software_name), array('name' => 'ASC'));
            //$softwares = $softwareRepository->findBy(array('name' => $software_name), array('name' => 'ASC'));
            
            if ($softwares == null) {
                $this->addFlash(
                    'errorSoftware', 'Software '.$software_name.' not found, please try again !'
                );
                return $this->redirectToRoute('software');
            }
            
            if ($family_name != null) {
                $families = $deviceFamilyRepository->findFamilyByNameAll($family_name->getName());
                //$families = $deviceFamilyRepository->findBy(array('name' => $family_name->getName()));
            }
            //return $this->redirectToRoute('software');
        }
        
        //$uploadform = $this->addSoftware($request, $dbRequest, $doctrine, $fileUploader, $deviceFamilyRepository);
        $this->addDirectorySoftware($dbRequest);

        $path = RESSOURCE_PATH;
        $package_path = PACK_PATH;
        echo("\r\n".$path."\r\n");
        //echo("\r\n".$path."WLE256_12_2_v000.028.bin\r\n");
        //echo("\r\n".$_ENV["CC_FS_BUCKET"]."\r\n");
        //echo("\r\n".$_ENV["APP_HOME"]."\r\n");
        if (file_exists($path)) {
            echo "\r\nhello path exists\r\n";
            $file = scandir($path);
            print_r ($file);
        }
        else {
            echo "\r\noh no\r\n";
        }
        if (file_exists($path."WLE256_12_2_v000.028.bin")) {
            if ($path."WLE256_12_2_v000.028.bin"!="https://app-6ca0bd63-0c52-456b-8979-16f58874abb4.cleverapps.io/home/bas/app_6ca0bd63-0c52-456b-8979-16f58874abb4/public/Ressource/Ressource/WLE256_12_2_v000.028.bin") {
                echo ("this is different");
            }
            echo "\r\nhello bin exists\r\n";
        }
        else {
            echo "\r\noh no bin not exists\r\n";
        }
        if (file_exists($package_path)) {
            echo "\r\nhello package exists\r\n";
        }
        else {
            echo "\r\noh no package not exists\r\n";
        }
        if (file_exists($_ENV["APP_HOME"].$_ENV["CC_FS_BUCKET"])) {
            //echo ($_ENV["APP_HOME"] . $_ENV["CC_FS_BUCKET"]);
            echo "\r\nhello fs exists\r\n";
        }
        else {
            echo ($_ENV["APP_HOME"] . $_ENV["CC_FS_BUCKET"]);
            echo "\r\noh no fs not exist\r\n";
        }
        if (file_exists($_ENV["APP_HOME"])) {
            echo "\r\nhello\r\n";
        }
        else {
            echo "\r\noh no\r\n";
        }

        return $this->render('software.html.twig', [
            'softwares' => $softwares,
            'software' => $software,
            //'form' => $form,
            'families' => $families,
            'searchform' => $searchform->createView(),
            //'uploaform' => $uploadform,
            'ressource_path' => $_ENV["RESSOURCE_PATH"],
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

    /**
     * Check Directory is copied in db
     * @param DbRequest $request
     * @return void
     */
    public function addDirectorySoftware(DbRequest $request)
    {
        // for each device type in device type array
        foreach (deviceType as $key => $deviceType) {
            if (file_exists(PACK_PATH.$deviceType)) {
                $arrayVersion[$deviceType] = array_diff(scandir(PACK_PATH.$deviceType), array('.'));

                array_shift($arrayVersion[$deviceType]);
                /*
                if (!file_exists(UPLOAD_PATH."softwares/".$deviceType)) {
                    mkdir(UPLOAD_PATH."softwares/".$deviceType);
                }
                */
                //if record not in db, add record
                // for each file in device type folder, create record in db if not exists
                foreach ($arrayVersion[$deviceType] as $key => $file) {
                    if (str_ends_with($file, ".bin")) {
                        $fileArray = explode("v", $file);
                        $version = substr($fileArray[1], -11, 7);
                        $deviceTypeId = $request->getDeviceType(deviceId[$deviceType], ID);
                        //$deviceTypeName = str_replace('/', '', $deviceType);
                        /*
                        if (!file_exists(UPLOAD_PATH."softwares/".$deviceType.$file)) {
                            copy(PACK_PATH.$deviceType.$file, UPLOAD_PATH."softwares/".$deviceType.$file);
                        }
                        */
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
     * @Route("/{_locale<%app.supported_locales%>}/user/software/add", name="software_add")
     * Upload new software with form
    */
    public function addSoftware(Request $request, DbRequest $dbRequest, ManagerRegistry $doctrine, FileUploader $fileUploader, DeviceFamilyRepository $deviceFamilyRepository, LoggerInterface $logger): Response
    {
        $software = new Software;
        $form = $this->createForm(SoftwareType::class, $software);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $softwareFile = $form->get('file')->getData();
            if ($softwareFile) {
                $fileName = $form->get('file')->getData()->getClientOriginalName();
                $deviceType = substr($fileName, 7, 2);
                $family = $deviceFamilyRepository->findOneBy(array('numberId'=>$deviceType));
                $originalFilename = $fileUploader->upload($softwareFile, "package/".$family->getName().'/');
                $softwareVersion = substr($fileName, -11, 7);
                $softwareName = $originalFilename;
                $pattern2 = '/-/i';
                $softwareVersionModified = preg_replace($pattern2, '.', $softwareVersion);
                $pattern3 = "/^0{1,2}/";
                $pattern4 = "/\.0{1,2}/";
                $softwareVersionModified2 = preg_replace($pattern3, '', $softwareVersionModified);
                $softwareVersionModified3 = preg_replace($pattern4, '.', $softwareVersionModified2);
                
                $software->setDeviceFamily($family);
                $software->setName($fileName);
                $software->setSoftwareFile($fileName);
                $software->setVersion($softwareVersionModified3);
                
                $user = $this->getUser();
                $logger->info($user." has uploaded ".$fileName);
                $this->updateSoftwareInDB($name=$fileName, $devType=$family->getId(), $version=$softwareVersionModified3, $date=date("Y-m-d | H:i:s"), $dbRequest);
                
            }
            $this->addFlash('infoSoftware', 'Software '.$fileName.' added with success !');
            return $this->redirectToRoute('software');
        }
        return $this->renderForm('software/add.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/admin/software/edit/{id}", name="software_edit")
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
     * @Route("/{_locale<%app.supported_locales%>}/user/software/delete/{deviceFamily}/{id}", name="software_delete")
    */    
    public function deleteSoftware(Software $software, ManagerRegistry $doctrine, LoggerInterface $logger, DeviceFamily $deviceFamily, $id)
    {
        $filesystem = new Filesystem();
        // récupère le nom du software
        $softwares = $deviceFamily->getSoftwares();
        /*
        $indexOf = $softwares->indexOf($software);
        echo($indexOf);
        $soft = $softwares->get($indexOf);
        */
        $soft = $software;
        $name = $soft->getName();
        $deviceType = $soft->getDeviceFamily()->getName();
        /*
        if (file_exists($this->getParameter('softwares_directory').'/'.$deviceType."/".$name)) {
            unlink($this->getParameter('softwares_directory').'/'.$deviceType."/".$name);
        }
        */
        if (file_exists($_ENV["PACK_PATH"].'/'.$deviceType."/".$name)) {
            unlink($_ENV["PACK_PATH"].'/'.$deviceType."/".$name);
        }
        /*
        if (file_exists($this->getParameter('archives_directory').'/'.$deviceType."/".$name)) {
            unlink($this->getParameter('archives_directory').'/'.$deviceType."/".$name);
        }
        */
        if (file_exists($_ENV["PACK_ARCH_PATH"].'/'.$deviceType."/".$name)) {
            unlink($_ENV["PACK_ARCH_PATH"].'/'.$deviceType."/".$name);
        }
        /*
        if (file_exists($this->getParameter('uploads_directory').'/'.'softwares/'.$deviceType."/".$name)) {
            unlink($this->getParameter('uploads_directory').'/'.'softwares/'.$deviceType."/".$name);
        }
        */
        if (file_exists($_ENV["UPLOAD_PATH"].'/'.'softwares/'.$deviceType."/".$name)) {
            unlink($_ENV["UPLOAD_PATH"].'/'.'softwares/'.$deviceType."/".$name);
        }
        $em = $doctrine->getManager();
        $deviceFamily->removeSoftware($soft);
        $em->remove($software);
        $em->flush();

        $user = $this->getUser();
        $logger->info($user." has deleted ".$name);
        $this->addFlash('message', 'Software '.$name.' deleted with success !');
        return $this->redirectToRoute('software');
    }

    /**
     * @Route("/addUpdateComment/{id}/{comment}", name="add_update_comment")
     */
    public function addUpdateComment(ManagerRegistry $doctrine, SoftwareRepository $softwareRepository, $id, $comment, LoggerInterface $logger) {
        $user = $this->getUser();
        $software = $softwareRepository->findOneBy(array('id' => $id));
        if ($comment == "null") {
            $comment = "";
            $logger->info($user." has deleted comment.");
            $this->addFlash('message', 'Comment deleted with success !');
        }
        else {
            $logger->info($user." has added comment ".$comment);
            $this->addFlash('message', 'Comment '.$comment.' added with success !');
        }
        $software->setUpdateComment($comment);

        $em = $doctrine->getManager();
        $em->persist($software);
        $em->flush();
        

        //return new Response("true");
        return $this->redirectToRoute('software');
        
    }

    /**
     * @Route("/addActualVersion/{id}/{version}/", name="add_actual_version")
     */
    public function addActualVersion(ManagerRegistry $doctrine, DeviceFamilyRepository $deviceFamilyRepository, SoftwareRepository $softwareRepository, $id, $version, LoggerInterface $logger) {
        $user = $this->getUser();
        $deviceFamily = $deviceFamilyRepository->findOneBy(array('id' => $id));
        $software = $softwareRepository->findOneBy(array('deviceFamily' => $id, 'version' => $version));
        if ($version == "null") {
            $version = "";
            $logger->info($user." has deleted actual version ".$version." for device family ".$deviceFamily);
            $this->addFlash('message', 'Actual Version deleted with success !');
        }
        else {
            $logger->info($user." has added updated actual version ".$version." for device family ".$deviceFamily);
            $this->addFlash('message', 'Actual Version '.$version." updated with success for device family ".$deviceFamily."!");
        }
        $deviceFamily->setActualVersion($software);

        $em = $doctrine->getManager();
        $em->persist($deviceFamily);
        $em->flush();
        

        //return new Response("true");
        return $this->redirectToRoute('software');
        
    }
}