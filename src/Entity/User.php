<?php

namespace Vinorcola\PrivateUserBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping;
use LogicException;
use Ramsey\Uuid\Uuid;
use Vinorcola\PrivateUserBundle\Model\BaseUser;
use function mb_strtolower;

#[Mapping\Entity]
#[Mapping\Table(name: '`user`')]
class User extends BaseUser
{
    #[Mapping\Column(type: 'guid')]
    #[Mapping\Id]
    private string $id;

    #[Mapping\Column(type: 'string', length: 254)]
    private string $emailAddress;

    #[Mapping\Column(type: 'string', length: 254, unique: true)]
    private string $uniqueEmailAddress;

    #[Mapping\Column(type: 'string', length: 80)]
    private string $firstName;

    #[Mapping\Column(type: 'string', length: 80)]
    private string $lastName;

    #[Mapping\Column(type: 'json', options: [
        'jsonb' => true,
    ])]
    private array $roles;

    /**
     * Note: Not saved in database since is it processed from roles.
     */
    private string|null $type;

    #[Mapping\Column(type: 'string', length: 60, nullable: true)]
    private string|null $password;

    #[Mapping\Column(type: 'boolean')]
    private bool $enabled;

    #[Mapping\Column(type: 'guid', nullable: true)]
    private string|null $token;

    #[Mapping\Column(type: 'datetime', nullable: true)]
    private DateTime|null $tokenExpirationDate;

    /**
     * User constructor.
     *
     * @param string[] $roles
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
     * {@inheritDoc}
     */
    public function setName(string $firstName, string $lastName): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * {@inheritDoc}
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * {@inheritDoc}
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * {@inheritDoc}
     */
    public function generateToken(DateTime $tokenExpirationDate)
    {
        $this->token = Uuid::uuid4()->toString();
        $this->tokenExpirationDate = $tokenExpirationDate;
    }

    /**
     * {@inheritDoc}
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
            throw new LogicException('User type has not been set.');
        }

        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function isActivated(): bool
    {
        return $this->password !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenExpirationDate(): ?DateTimeInterface
    {
        return $this->tokenExpirationDate;
    }
}
