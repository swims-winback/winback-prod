<?php

namespace App\Security;

use App\Entity\Customer\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\KeycloakClient;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class KeycloakAuthenticator
 */
class KeycloakAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        
        return new RedirectResponse(
            '/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
        
    }

    
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'oauth_check';
    }
    
    
    public function authenticate(Request $request): Passport
    {
        //$client = $this->clientRegistry->getClient('keycloak');
        $client = $this->getKeycloakClient();
        $accessToken = $this->fetchAccessToken($client);
        //$password = $request->request->get('password');
        //$username = $request->request->get('username');
        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /// @var GoogleUser $googleUser /
                $keycloakUser = $client->fetchUserFromToken($accessToken);

                $email = $keycloakUser->getEmail();
                $firstname = $keycloakUser->getFirstname();

                if (null === $email) {
                    throw new UsernameNotFoundException(sprintf('User "%s" not found.', $email));
                }

                // have they logged in with Google before? Easy!
                //$existingUser = $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);
                $existingUser = $this->em->getRepository(User::class)->findOneBy(['keycloakId' => $keycloakUser->getId()]);
                //$existingUser = $this->em->getRepository(User::class)->findOneBy(['id' => $keycloakUser->getId()]);
                //User doesnt exist, we create it !
                if (!$existingUser) {
                    $existingUser = new User();
                    $existingUser->setEmail($email);
                    $existingUser->setUsername($firstname);
                    $existingUser->setRoles(['ROLE_USER']);
                    $existingUser->setKeycloakId($keycloakUser->getId());
                    $this->em->persist($existingUser);
                }
                $this->em->flush();

                return $existingUser;
            })
        );
    }
    
    /*
    public function getCredentials(Request $request) {
        return $this->fetchAccessToken($this->getKeycloakClient());
    }
    */
    /*
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $keycloakUser = $this->getKeycloakClient()->fetchUserFromToken($credentials['']);
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['keycloakId' => $keycloakUser->getId()]);
        if ($existingUser) {
            return $existingUser;
        }
        $email = $keycloakUser->getEmail();
        $userInDatabase = $this->em->getRepository(User::class)->findOneBy(['email'=> $email]);
        if ($userInDatabase) {
            $userInDatabase->setKeycloakId($keycloakUser->getId());
            $this->em->persist($userInDatabase);
            $this->em->flush();
            return $userInDatabase;
        }
        $user = new User();
        $user->setKeycloakId($keycloakUser->getId());
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }
    */

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $targetUrl = $this->router->generate('home');

        return new RedirectResponse($targetUrl);
    }


    /**
     * Summary of getKeycloakClient
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    private function getKeycloakClient()
    {
        return $this->clientRegistry->getClient('keycloak');
    }
}
