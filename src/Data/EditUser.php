<?php

namespace Vinorcola\PrivateUserBundle\Data;

use Vinorcola\PrivateUserBundle\Model\UserInterface;

class EditUser
{
    /**
     * @var string
     */
    public $type;

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
        $dto->type = $user->getType();
        $dto->emailAddress = $user->getEmailAddress();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->enabled = $user->isEnabled();

        return $dto;
    }
}
