<?php

namespace Vinorcola\PrivateUserBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping;
use Ramsey\Uuid\Uuid;
use Vinorcola\PrivateUserBundle\Model\BaseUser;

/**
 * @Mapping\Entity()
 * @Mapping\Table(name="`user`")
 */
class User extends BaseUser
{
    /**
     * @var string
     *
     * @Mapping\Column(type="guid")
     * @Mapping\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @Mapping\Column(type="string", length=254)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @Mapping\Column(type="string",length=254, unique=true)
     */
    private $uniqueEmailAddress;

    /**
     * @var string
     *
     * @Mapping\Column(type="string", length=80)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Mapping\Column(type="string", length=80)
     */
    private $lastName;

    /**
     * @var array
     *
     * @Mapping\Column(type="json", options={"jsonb": true})
     */
    private $roles;

    /**
     * @var string|null
     *
     * Note: Not save in database since is it processed from roles.
     */
    private $type;

    /**
     * @var string|null
     *
     * @Mapping\Column(type="string", length=60, nullable=true)
     */
    private $password;

    /**
     * @var bool
     *
     * @Mapping\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var string|null
     *
     * @Mapping\Column(type="guid", nullable=true)
     */
    private $token;

    /**
     * @var DateTime|null
     *
     * @Mapping\Column(type="datetime", nullable=true)
     */
    private $tokenExpirationDate;

    /**
     * User constructor.
     *
     * @param string $emailAddress
     * @param string $firstName
     * @param string $lastName
     * @param array  $roles
     */
    public function __construct(string $emailAddress, string $firstName, string $lastName, array $roles = [])
    {
        $this->id = Uuid::uuid4()->toString();
        $this->emailAddress = $emailAddress;
        $this->uniqueEmailAddress = mb_strtolower($emailAddress);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
        $this->password = null;
        $this->enabled = true;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
        $this->uniqueEmailAddress = mb_strtolower($emailAddress);
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $firstName, string $lastName): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(DateTime $tokenExpirationDate)
    {
        $this->token = Uuid::uuid4()->toString();
        $this->tokenExpirationDate = $tokenExpirationDate;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseToken()
    {
        $this->token = null;
        $this->tokenExpirationDate = null;
    }

    /**
     * Return unique identifier.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if ($this->type === null) {
            throw new \LogicException('User type has not been set.');
        }

        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string|null
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return DateTime|null
     */
    public function getTokenExpirationDate(): DateTime
    {
        return $this->tokenExpirationDate;
    }
}
