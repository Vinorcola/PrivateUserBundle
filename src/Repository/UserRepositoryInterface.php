<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use Vinorcola\PrivateUserBundle\Model\UserInterface;

interface UserRepositoryInterface
{
    /**
     * Persist the user in the repository.
     *
     * @param UserInterface $user
     */
    public function add(UserInterface $user): void;

    /**
     * Find the user identified by the given email address.
     *
     * @param string $emailAddress
     * @return UserInterface|null
     */
    public function find(string $emailAddress): ?UserInterface;
}
