<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\Client\ClientRegistrationType;
use App\Repository\ClientRepository;
use App\Repository\DeviceRepository;
use App\Repository\SnRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/user/client', name: 'app_client')]
    public function index(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository, ClientRepository $clientRepository, SnRepository $snRepository, DeviceRepository $deviceRepository): Response
    {
        $clientSnArray = [];
        $clientDeviceArray = [];
        $username = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(array('username' => $username));
        $email = $user->getEmail();
        if (($clients = $clientRepository->findBy(array('email' => $email)))!=false) {
            foreach ($clients as $client) {
                $clientSn = $client->getSerialNumber();
                $sn = $snRepository->findOneBy(array('SN' => $clientSn));
                $device = $deviceRepository->findOneBy(array('sn' => $clientSn));
                array_push($clientSnArray, $sn);
                array_push($clientDeviceArray, $device);
                // make an array with sn as key, sn object as value, device object as value
                // add clientSn to clientSnArray
            }
        }
        
        //$form = $this->createForm(ClientRegistrationType::class, $client);
        $form = $this->createForm(ClientRegistrationType::class);
        $form->handleRequest($request);
        $entityManager = $doctrine->getManager();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $email = $form->get('email')->getData();
            $client->setEmail($email);
            $client->setSerialNumber($form->get('serial_number')->getData());

            $entityManager->persist($client);
            $entityManager->flush();
            //$user = $userRepository->findOneBy(array('email' => $email));
            return $this->redirectToRoute('app_client');
        }
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
            'clientSnArray' => $clientSnArray,
            'clientDeviceArray' => $clientDeviceArray,
            'clientRegistration' => $form->createView(),
        ]);
    }

    /*
    #[Route('client/sn/{id}', name: 'client_sn')]
    public function show_sn($client_id) {
        echo($client_id);
        return $this->render('client/sn.html.twig', [
            new Response(True)
        ]);
    }
    */
}
