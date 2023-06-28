<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\Client\ClientRegistrationType;
use App\Repository\ClientRepository;
use App\Repository\DeviceRepository;
use App\Repository\DeviceServerRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/", name="home")
     */
    public function index(Request $request, ManagerRegistry $doctrine, ClientRepository $clientRepository, UserRepository $userRepository, DeviceServerRepository $deviceServerRepository, DeviceRepository $deviceRepository): Response
    {

        $username = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(array('username' => $username));
        $email = $user->getEmail();
        $clientIdentified = $clientRepository->findBy(array('email' => $email));

        $deviceCount_array = $this->getDeviceServer($deviceServerRepository); //number of devices connected by day
        $deviceCreated_array = $this->getDeviceCreated($deviceRepository); // number of devices created by day
        //print_r($deviceCreated_array);
        /*
        if (($clientIdentified = $clientRepository->findBy(array('email' => $email)))!=false) {
            
            foreach ($clientIdentified as $client) {
                $clientSn = $client->getSerialNumber();
                $sn = $snRepository->findOneBy(array('SN' => $clientSn));
                $device = $deviceRepository->findOneBy(array('sn' => $clientSn));
                array_push($clientSnArray, $sn);
                array_push($clientDeviceArray, $device);
                // make an array with sn as key, sn object as value, device object as value
                // add clientSn to clientSnArray
            }
            
        }
        */

        $form = $this->createForm(ClientRegistrationType::class);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $email = $form->get('email')->getData();
            $client->setEmail($email);
            //$client->setSerialNumber($form->get('serial_number')->getData());

            $entityManager->persist($client);
            $entityManager->flush();
            //$user = $userRepository->findOneBy(array('email' => $email));
            return $this->redirectToRoute('home');
        }
        
        return $this->render('main/index.html.twig', [
            'clientRegistration' => $form->createView(),
            'clientIdentified' => $clientIdentified,
            'deviceCountArray'=>$deviceCount_array,
            'deviceCreatedArray'=>$deviceCreated_array
        ]);
    }


    /**
     * @Route("/devicesConnected/", name="get_devices_connected")
     */
    function getDeviceServer(DeviceServerRepository $deviceServerRepository) {
        //find 7 days before today
        $i = 0;
        $date_array = [];
        $deviceCount_array = [];
        while ($i <= 7) {
            $date = strtotime("-{$i} day");
            $date_array[] = date('Y-m-d', $date);
            $i++;
        }
        $date_array = array_reverse($date_array);
        //find number of devices connected on date given
        foreach ($date_array as $date) {
            $allDevices = $deviceServerRepository->findByDate($date);
            $deviceCount_array[$date] = count($allDevices);
        }
        return ($deviceCount_array);
    }

    /**
     * @Route("/devicesCreated/", name="get_devices_created")
     */
    function getDeviceCreated(DeviceRepository $deviceRepository) {
        $i = 0;
        $date_array = [];
        $deviceCount_array = [];
        while ($i <= 7) {
            $date = strtotime("-{$i} day");
            $date_array[] = date('Y-m-d', $date);
            $i++;
        }
        $date_array = array_reverse($date_array);
        //find number of devices connected on date given
        foreach ($date_array as $date) {
            $allDevices = $deviceRepository->findByDate($date);
            //$allDevices = $deviceServerRepository->findByDate($date);
            $deviceCount_array[$date] = count($allDevices);
        }
        return ($deviceCount_array);
    }
}