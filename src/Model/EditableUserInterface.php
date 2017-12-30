<?php

namespace Vinorcola\PrivateUserBundle\Model;

interface EditableUserInterface
{
    /**
     * Change the user's first and last names.
     *
     * @param string $firstName
     * @param string $lastName
     */
    public function setName(string $firstName, string $lastName): void;

    /**
     * Change the user's roles.
     *
     * @param array $roles
     */
    public function setRoles(array $roles): void;

    /**
     * Enable the user.
     *
     * An enabled user can connect to the application.
     */
    public function enable(): void;

    /**
     * Disable the user.
     *
     * A disabled user cannot connect to the application.
     */
    public function disable(): void;
}
