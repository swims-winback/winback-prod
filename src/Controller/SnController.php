<?php

namespace App\Controller;

use App\Class\SearchSn;
use App\Form\SearchSnType;
use App\Repository\SnRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SnController extends AbstractController
{
    #[Route('/sn', name: 'app_sn')]
    public function index(SnRepository $snRepository, Request $request): Response
    {
        //$sn = $snRepository->findAll();
        $data = new SearchSn();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchSnType::class, $data);
        $form->handleRequest($request);
        $sn = $snRepository->findSearch($data);

        return $this->render('sn.html.twig', [
            //'controller_name' => 'SnController',
            'sn' => $sn,
            'form' => $form->createView(),
        ]);
    }
}
