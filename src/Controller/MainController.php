<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\Client\ClientRegistrationType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/", name="home")
     */
    public function index(Request $request, ManagerRegistry $doctrine, ClientRepository $clientRepository, UserRepository $userRepository): Response
    {

        $username = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findOneBy(array('username' => $username));
        $email = $user->getEmail();
        $clientIdentified = $clientRepository->findBy(array('email' => $email));
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
            $client->setSerialNumber($form->get('serial_number')->getData());

            $entityManager->persist($client);
            $entityManager->flush();
            //$user = $userRepository->findOneBy(array('email' => $email));
            return $this->redirectToRoute('home');
        }
        
        return $this->render('main/index.html.twig', [
            'clientRegistration' => $form->createView(),
            'clientIdentified' => $clientIdentified
        ]);
    }
}