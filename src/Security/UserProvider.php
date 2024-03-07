<?php

namespace App\Security;

use App\Entity\Customer\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserProvider implements OAuthAwareUserProviderInterface
{
    private $em;
    private $property = 'email';

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        $repository = $this->em->getRepository(User::class);

        if (null !== $this->property) {
            $user = $repository->findOneBy([$this->property => $username]);
        } else {
            if (!$repository instanceof UserLoaderInterface) {
                throw new \InvalidArgumentException(sprintf('You must either make the "%s" entity Doctrine Repository ("%s") implement "Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface" or set the "property" option in the corresponding entity provider configuration.', $this->classOrAlias, \get_class($repository)));
            }
            $user = $repository->loadUserByUsername($username);
        }

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        return $user;
    }

    /**
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', User::class));
        }

        $repository = $this->em->getRepository(User::class);

        if ($repository instanceof UserProviderInterface) {
            $refreshedUser = $repository->refreshUser($user);
        } else {
            $refreshedUser = $repository->find($user->getId());
            if (null === $refreshedUser) {
                throw new UsernameNotFoundException(sprintf('User with id %s not found', json_encode($user->getId())));
            }
        }

        return $refreshedUser;
    }

    /**
     * @return UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        return $this->loadUserByUsername($response->getEmail());
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}