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
     * Find all the users.
     *
     * @return UserInterface[]
     */
    public function findAll(): array;

    /**
     * Find the user identified by the given email address.
     *
     * @param string $emailAddress
     * @return EditableUserInterface|null
     */
    public function find(string $emailAddress): ?EditableUserInterface;

    /**
     * Find the user identified by the given registration token.
     *
     * Note that function must take care of token expiration date. If token is expired, no user must be returned.
     *
     * @param string $token
     * @return EditableUserInterface|null
     */
    public function findByRegistrationToken(string $token): ?EditableUserInterface;

    /**
     * Find the user identified by the given password change token.
     *
     * Note that function must take care of token expiration date. If token is expired, no user must be returned.
     *
     * @param string $token
     * @return EditableUserInterface|null
     */
    public function findByPasswordChangeToken(string $token): ?EditableUserInterface;
}
