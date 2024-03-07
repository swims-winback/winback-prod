<?php

namespace App\Controller;

use App\Entity\Customer\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/manager', name: 'app_user')]
    //public function index(UserRepository $userRepository): Response
    public function index(ManagerRegistry $doctrine): Response
    {
        //$users = $userRepository->findAll();
        $users = $doctrine->getRepository(User::class, 'customer')->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }
}
