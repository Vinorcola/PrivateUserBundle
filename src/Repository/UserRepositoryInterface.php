<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;

interface UserRepositoryInterface
{
    /**
     * Persist the user in the repository.
     *
     * @param EditableUserInterface $user
     */
    public function add(EditableUserInterface $user): void;

    /**
     * Find the user identified by the given email address.
     *
     * @param string $emailAddress
     * @return EditableUserInterface|null
     */
    public function find(string $emailAddress): ?EditableUserInterface;
}
