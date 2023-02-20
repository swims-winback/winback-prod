<?php

namespace App\Controller;

use App\Entity\Statistics;
use App\Form\SearchStatisticsType;
use App\Form\StatisticsUploadType;
use App\Repository\StatisticRepository;
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
    #[Route('/{_locale<%app.supported_locales%>}/admin/statistics', name: 'statistics')]
    public function index(StatisticRepository $statisticRepository, Request $request): Response
    {
        //if form is submitted
        $filter_form = $this->createForm(SearchStatisticsType::class);
        $filter_form->handleRequest($request);

        if($filter_form->isSubmitted() && $filter_form->isValid())
        {
            if (($tool_object = $filter_form->get('accessoires')->getData())!=null) {
                $tool_filter = $tool_object->getAccessoires();
            }
            else {
                $tool_filter = null;
            }
            if (($patho_object = $filter_form->get('patho')->getData())) {
                $patho_filter = $patho_object->getPatho();
            }
            else {
                $patho_filter = null;
            }
            if (($type_patho_object = $filter_form->get('type_patho')->getData())!=null) {
                $type_patho_filter = $type_patho_object->getTypePatho();
            }
            else {
                $type_patho_filter = null;
            }
            if (($sn_object = $filter_form->get('SN')->getData())!=null) {
                $sn_filter = $sn_object->getSn();
            }
            else {
                $sn_filter = null;
            }
            if (($zone_object = $filter_form->get('zone')->getData())!=null) {
                $zone_filter = $zone_object->getZone();
            }
            else {
                $zone_filter = null;
            }

        }
        else {
            $tool_filter = null;
            $patho_filter = null;
            $type_patho_filter=null;
            $sn_filter=null;
            $zone_filter = null;
        }
        /* Get treatments number by sn */
        $deviceArray = $statisticRepository->findAllDevice($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by patho type */
        $pathoTypeArray = $statisticRepository->findAllPathotype($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by zones */
        $zoneArray = $statisticRepository->findAllZone($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by patho */
        $pathoArray = $statisticRepository->findAllPatho($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by tools */
        $accessoireArray = $statisticRepository->findAllTool($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);

        return $this->render('statistics.html.twig', [
            //'controller_name' => 'StatisticsController',
            //'devices' => $devices,
            'deviceArray' => $deviceArray,
            'pathoTypeArray' => $pathoTypeArray,
            'zoneArray' => $zoneArray,
            'pathoArray' => $pathoArray,
            'toolArray' => $accessoireArray,
            'filterForm' => $filter_form->createView()
        ]);
    }
    /**
     * @Route("/filterSn/{data}/", name="get_filter_sn")
     */
    public function getFilterSn($data) {
        echo ($data);
        return $data;
    }
     //@Route("/filter/{category}/{element}/", name="get_filter")
    /**
     * @Route("/{_locale<%app.supported_locales%>}/filter/{category}/", name="get_filter")
     * @param StatisticRepository $statisticRepository
     * @param mixed $category
     * @param mixed $element
     * @return Response
     */
    public function getFilter(StatisticRepository $statisticRepository, $category)
    {
        //$category = 'zone' for example
        //$element = $zone for example
        print_r($category);
        //print_r($element);
        if (isset($category['sn'])) {
            //$sn_filter = $element;
            $sn_filter = $category['sn'];
            print_r($sn_filter);
        }
        /*
        if ($category == 'sn') {
            $sn_filter = $element;
        }
        elseif ($category == 'zone') {
            $zone_filter = $element;
        }
        elseif ($category == 'patho') {
            $patho_filter = $element;
        }
        */
        if (!(isset($sn_filter))) {
            $sn_filter=null;
        }
        if (!(isset($type_patho_filter))) {
            $type_patho_filter=null;
        }
        if (!(isset($zone_filter))) {
            $zone_filter =null;
        }
        if (!(isset($patho_filter))) {
            $patho_filter = null;
        }
        if (!(isset($tool_filter))) {
            $tool_filter = null;
        }

        echo("\r\nsn: ".$sn_filter);
        echo ("\r\npatho type: ".$type_patho_filter);
        echo ("\r\nzone: ".$zone_filter);
        echo ("\r\npatho: ".$patho_filter);
        echo ("\r\ntool: ".$tool_filter);

        /* Get treatments number by sn */
        $deviceArray = $statisticRepository->findAllDevice($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by patho type */
        $pathoTypeArray = $statisticRepository->findAllPathotype($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by zones */
        $zoneArray = $statisticRepository->findAllZone($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by patho */
        $pathoArray = $statisticRepository->findAllPatho($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);
        /* Get treatments number by tools */
        $accessoireArray = $statisticRepository->findAllTool($sn_filter, $type_patho_filter, $zone_filter, $patho_filter, $tool_filter);

        
        return $this->render('statistics.html.twig', [
            'deviceArray' => $deviceArray,
            'pathoTypeArray' => $pathoTypeArray,
            'zoneArray' => $zoneArray,
            'pathoArray' => $pathoArray,
            'toolArray' => $accessoireArray
        ]);
        
        //return new Response(True);
    }

    //#[Route('/admin/statistics/', name: 'statistics')]
    /*
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
    */
}

