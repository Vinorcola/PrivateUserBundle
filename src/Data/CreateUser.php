<?php

namespace Vinorcola\PrivateUserBundle\Data;

class CreateUser
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
     * @var bool
     */
    public $sendInvitation = true;
}
