<?php

namespace Vinorcola\PrivateUserBundle\Model;

abstract class BaseUser implements EditableUserInterface
{
    /**
     * {@inheritdoc}
     * Not used: use an algorithm that generate the salt (example: bcrypt).
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmailAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {

    }
}
