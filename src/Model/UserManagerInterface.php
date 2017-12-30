<?php

namespace Vinorcola\PrivateUserBundle\Model;

use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Data\EditUser;

interface UserManagerInterface
{
    /**
     * Create a new user using data in the dto.
     *
     * @param CreateUser $dto
     * @return UserInterface
     */
    public function create(CreateUser $dto): UserInterface;

    /**
     * Update a user using data in the dto.
     *
     * @param EditableUserInterface $user
     * @param EditUser              $dto
     * @return mixed
     */
    public function update(EditableUserInterface $user, EditUser $dto);
}
