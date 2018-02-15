<?php

namespace Vinorcola\PrivateUserBundle\Model;

use DateTime;

interface EditableUserInterface extends UserInterface
{
    /**
     * Change the user's email address.
     *
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void;

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
     * Set a new password for the user account.
     *
     * @param string $password
     */
    public function setPassword(string $password);

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

    /**
     * Generate a new token for activation or password change.
     *
     * @param DateTime $tokenExpirationDate
     */
    public function generateToken(DateTime $tokenExpirationDate);

    /**
     * Erase the activation or change password token.
     */
    public function eraseToken();
}
