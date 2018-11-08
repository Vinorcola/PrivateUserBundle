<?php

namespace Vinorcola\PrivateUserBundle\Model;

use DateInterval;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vinorcola\PrivateUserBundle\Data\ChangePassword;
use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Data\EditUser;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Repository\UserRepositoryInterface;

class UserManager implements UserManagerInterface
{
    /**
     * Validity period (in minute) of a user token send by e-mail.
     */
    private const TOKEN_VALIDITY = 20;

    /**
     * @var UserRepositoryInterface
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Config
     */
    private $config;

    /**
     * UserManager constructor.
     *
     * @param UserRepositoryInterface      $repository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface        $tokenStorage
     * @param Config                       $config
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage,
        Config $config
    ) {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
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

    private function getComparableRoles(array $roles): string
    {
        // Sort and serialize.
        sort($roles);

        return serialize($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function create(CreateUser $data): UserInterface
    {
        $user = new User($data->emailAddress, $data->firstName, $data->lastName, $this->config->getRoles($data->type));
        $this->repository->add($user);

        return $user;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function updatePassword(EditableUserInterface $user, ChangePassword $data): void
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $data->password));
        $user->eraseToken();
    }

    /**
     * {@inheritdoc}
     */
    public function generateToken(EditableUserInterface $user): void
    {
        $validity = new DateTime();
        $validity->add(new DateInterval('PT' . self::TOKEN_VALIDITY . 'M'));
        $user->generateToken($validity);
    }

    /**
     * {@inheritdoc}
     */
    public function logUserIn(UserInterface $user): void
    {
        $this->tokenStorage->setToken(
            new UsernamePasswordToken($user, null, 'main', $user->getRoles())
        );
    }
}
