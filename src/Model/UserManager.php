<?php

namespace Vinorcola\PrivateUserBundle\Model;

use DateInterval;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Vinorcola\PrivateUserBundle\Data\ChangePassword;
use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Data\EditUser;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;
use function serialize;
use function sort;

class UserManager implements UserManagerInterface
{
    /**
     * Validity period (in minute) of a user token send by e-mail.
     */
    protected const TOKEN_VALIDITY = 20;

    /**
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * @var UserPasswordHasherInterface
     */
    protected $passwordHasher;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Config
     */
    protected $config;

    /**
     * UserManager constructor.
     *
     * @param UserRepositoryInterface     $repository
     * @param UserPasswordHasherInterface $passwordHasher
     * @param TokenStorageInterface       $tokenStorage
     * @param Config                      $config
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserPasswordHasherInterface $passwordHasher,
        TokenStorageInterface $tokenStorage,
        Config $config
    ) {
        $this->repository = $repository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenStorage = $tokenStorage;
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserType(UserInterface $user): string
    {
        // NOTE: We sort roles in order to compare them easily.
        $roles = $this->getComparableRoles($user->getRoles());
        foreach ($this->config->getUserTypes() as $userType) {
            if ($roles === $this->getComparableRoles($this->config->getRoles($userType))) {
                return $userType;
            }
        }

        return self::UNKNOWN_USER_TYPE;
    }

    protected function getComparableRoles(array $roles): string
    {
        // Sort and serialize.
        sort($roles);

        return serialize($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function create(CreateUser $data): UserInterface
    {
        $user = new User($data->emailAddress, $data->firstName, $data->lastName, $this->config->getRoles($data->type));
        $this->repository->add($user);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function update(EditableUserInterface $user, EditUser $data): void
    {
        $user->setEmailAddress($data->emailAddress);
        $user->setName($data->firstName, $data->lastName);
        $user->setType($data->type);
        $user->setRoles($this->config->getRoles($data->type));
        $data->enabled ? $user->enable() : $user->disable();
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(EditableUserInterface $user, ChangePassword $data): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->newPassword));
        $user->eraseToken();
    }

    /**
     * {@inheritDoc}
     */
    public function generateToken(EditableUserInterface $user): void
    {
        $validity = new DateTime();
        $validity->add(new DateInterval('PT' . self::TOKEN_VALIDITY . 'M'));
        $user->generateToken($validity);
    }

    /**
     * {@inheritDoc}
     */
    public function logUserIn(UserInterface $user): void
    {
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
    }
}
