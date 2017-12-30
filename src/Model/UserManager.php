<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Data\EditUser;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

class UserManager implements UserManagerInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * UserManager constructor.
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
    public function create(CreateUser $dto): UserInterface
    {
        $user = new User($dto->emailAddress, $dto->firstName, $dto->lastName);
        $this->repository->add($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function update(EditableUserInterface $user, EditUser $dto)
    {
        $user->setName($dto->firstName, $dto->lastName);
        $user->setRoles($dto->roles);
        $dto->enabled ? $user->enable() : $user->disable();
    }
}
