<?php

namespace Vinorcola\PrivateUserBundle\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;
use Vinorcola\PrivateUserBundle\Model\UserInterface as PrivateUserInterface;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
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
     * {@inheritDoc}
     */
    public function loadUserByIdentifier(string $emailAddress): UserInterface
    {
        $user = $this->repository->findEnabled($emailAddress);
        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     * @deprecated Use loadByIdentifier() instead.
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!($user instanceof PrivateUserInterface)) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByIdentifier($user->getEmailAddress());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class): bool
    {
        return is_subclass_of($class, PrivateUserInterface::class);
    }

    /**
     * {@inheritDoc}
     * @param PrivateUserInterface $user
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if ($user instanceof EditableUserInterface) {
            $user->setPassword($newHashedPassword);
        }
        $this->repository->updatePassword($user);
    }
}
