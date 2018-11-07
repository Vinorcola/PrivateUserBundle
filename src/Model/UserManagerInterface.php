<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Vinorcola\PrivateUserBundle\Data\ChangePassword;
use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Data\EditUser;

interface UserManagerInterface
{
    /**
     * Get the type of user.
     *
     * @param UserInterface $user
     * @return string|null
     */
    public function getUserType(UserInterface $user): ?string;

    /**
     * Create a new user using data in the data.
     *
     * @param CreateUser $data
     * @return UserInterface
     */
    public function create(CreateUser $data): UserInterface;

    /**
     * Update a user using data in the data.
     *
     * @param EditableUserInterface $user
     * @param EditUser              $data
     */
    public function update(EditableUserInterface $user, EditUser $data): void;

    /**
     * Update the user's password.
     *
     * @param EditableUserInterface $user
     * @param ChangePassword        $data
     */
    public function updatePassword(EditableUserInterface $user, ChangePassword $data): void;

    /**
     * Generate a token for a user.
     *
     * @param EditableUserInterface $user
     */
    public function generateToken(EditableUserInterface $user): void;

    /**
     * Log the user in.
     *
     * @param UserInterface $user
     */
    public function logUserIn(UserInterface $user): void;
}
