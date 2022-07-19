<?php

namespace App\Controller;

use App\Entity\Statistics;
use App\Form\StatisticsUploadType;
use App\Services\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StatisticsController extends AbstractController
{
/*     #[Route('/admin/statistics', name: 'statistics')]
    public function index(): Response
    {
        return $this->render('statistics.html.twig', [
            'controller_name' => 'StatisticsController',
        ]);
    } */

    #[Route('/admin/statistics/', name: 'statistics')]
    public function uploadStatisticsFile(Request $request, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $statistics = new Statistics;
        $form = $this->createForm(StatisticsUploadType::class, $statistics);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $statisticsFile = $form->get('file')->getData();

            if ($statisticsFile) {
                $originalFilename = $fileUploader->upload($statisticsFile, 'statistics/');
                $statistics->setStatisticsFile($originalFilename);
                
                $fileFolder = $this->getParameter('statistics_directory');
                $fileJson = file_get_contents($fileFolder.'/'.$originalFilename);
                $arr = json_decode($fileJson, true);

                $em = $doctrine->getManager();
                foreach($arr as $item) 
                {
                    $sn_id = $item['sn'];
                    $version = $item['version'];
                    $statistic_exists = $em->getRepository(Statistics::class)->findOneBy(array('sn' => $sn_id)); 
                    // make sure that the user does not already exists in your db 
                    if (!$statistic_exists)
                    { 
                        
                        var_dump($version);
                        $statistics = new Statistics(); 
                        $statistics->setVersion($version);

                        $em->persist($statistics); 
                        $em->flush();
                    }
                }

                //$package = new Symfony\Component\Asset\Package(new EmptyVersionStrategy());
                //$path = $package->getUrl('assets/json/test.json');
            }

        /*  $spreadsheet = IOFactory::load($fileFolder .'/'. $newFilename); // Here we are able to read from the excel file 
            $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line 
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
            dd($sheetData);
    
            $em = $doctrine->getManager(); 
            foreach ($sheetData as $Row)
            { 
    
                $version = $Row['Version']; // store the first_name on each iteration 
                $zone = $Row['Zone']; // store the last_name on each iteration
                $type= $Row['Type'];     // store the email on each iteration
                $patho = $Row['Patho'];   // store the phone on each iteration
    
                $statistic_exists = $em->getRepository(Statistics::class)->findOneBy(array('version' => $version)); 
                    // make sure that the user does not already exists in your db 
                if (!$statistic_exists)
                {   
                    $statistics = new Statistics(); 
                    $statistics->setVersion($version);           
                    $statistics->setZone($zone);
                    $statistics->setPathoType($type);
                    $statistics->setPathoName($patho);
                    $em->persist($statistics); 
                    $em->flush(); 
                        // here Doctrine checks all the fields of all fetched data and make a transaction to the database.
                } 
            }   */

            //$statistics->setStatisticsFile($originalFilename);
        }

        $em = $doctrine->getManager();
        $statistics = $form->getData();
        $em->persist($statistics);
        $em->flush();


        //return $this->json('statistics registered', 200);
        return $this->renderForm('statistics.html.twig', [
            'form' => $form,
        ]); 
    }

}

