<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    /**
     * Returns the user type.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Returns the email address used to authenticate the user.
     *
     * @return string The email address
     */
    public function getEmailAddress(): string;

    /**
     * Returns the first name of the user.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Returns the last name of the user.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Returns the name to display for the user.
     *
     * @return string The name to display
     */
    public function getDisplayName(): string;

    /**
     * Indicates if the user is enabled or not.
     *
     * Disabled user cannot connect to the application.
     *
     * @return bool
     */
    public function isEnabled(): bool;
}
