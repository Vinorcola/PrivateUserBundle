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
     * UserManager constructor.
     *
     * @param UserRepositoryInterface      $repository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenStorageInterface        $tokenStorage
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage
    ) {
        $this->repository = $repository;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function create(CreateUser $data): UserInterface
    {
        $user = new User($data->emailAddress, $data->firstName, $data->lastName, [ 'ROLE_USER' ]);
        $this->repository->add($user);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function update(EditableUserInterface $user, EditUser $data): void
    {
        $user->setName($data->firstName, $data->lastName);
        $user->setRoles($data->roles);
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
