<?php

namespace Vinorcola\PrivateUserBundle\Data;

use Vinorcola\PrivateUserBundle\Model\UserInterface;

class EditUser
{
    /**
     * @var string
     */
    public $emailAddress;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string[]
     */
    public $roles;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @param UserInterface $user
     * @return EditUser
     */
    public static function FromUser(UserInterface $user): self
    {
        $dto = new self;
        $dto->emailAddress = $user->getEmailAddress();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->roles = $user->getRoles();
        $dto->enabled = $user->isEnabled();

        return $dto;
    }
}
