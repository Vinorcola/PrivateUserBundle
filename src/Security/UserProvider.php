<?php

namespace Vinorcola\PrivateUserBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vinorcola\PrivateUserBundle\Model\UserInterface as PrivateUserInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * UserProvider constructor.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->repository->findEnabled($username);
        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!($user instanceof PrivateUserInterface)) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getEmailAddress());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, PrivateUserInterface::class);
    }
}
