<?php

namespace Vinorcola\PrivateUserBundle\Model;

abstract class BaseUser implements EditableUserInterface
{
    /**
     * {@inheritDoc}
     * Not used: use an algorithm that generate the salt (example: bcrypt).
     *
     * @return null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername(): string
    {
        return $this->getEmailAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmailAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function getDisplayName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials(): void
    {

    }
}
