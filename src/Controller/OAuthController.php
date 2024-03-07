<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OAuthController extends AbstractController
{
    
    #[Route('/')]
    public function indexNoLocale(): Response
    {
        //return $this->redirectToRoute('app_login', ['_locale' => 'en']);
        return $this->redirectToRoute('oauth_login', ['_locale' => 'en']);
    }
    
    
    #[Route(path: '/{_locale<%app.supported_locales%>}/', name: 'oauth_login')]
    //#[Route(path: '/', name: 'oauth_login')]
    public function index(ClientRegistry $clientRegistry)
    {
        /** @var KeycloakClient $client */
        $client = $clientRegistry->getClient('keycloak');
        return $client->redirect();
    }

    
    //@Route("/oauth/callback", name="oauth_check")
    //#[Route(path:'/{_locale<%app.supported_locales%>}/oauth/callback', name:'oauth_check')]
    #[Route(path: '/{_locale<%app.supported_locales%>}/logout', name: 'app_logout')]
    //#[Route(path:'/oauth/callback', name:'oauth_check')]
    //public function check(Request $request)
    public function logout(): void
    {
        //return new Response("true");
        throw new \Exception('');
    }


    #[Route(path:'/oauth/callback', name:'oauth_check')]
    public function check()
    {

    }
}
