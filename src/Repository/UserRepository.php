<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use DateTime;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;
use Vinorcola\PrivateUserBundle\Model\UserInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function add(EditableUserInterface $user): void
    {
        $this->entityManager->persist($user);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this
            ->createQueryBuilder('u')
            ->orderBy('u.firstName', 'ASC')
            ->addOrderBy('u.lastName', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $emailAddress): ?EditableUserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.emailAddress = :emailAddress')
            ->setParameter('emailAddress', $emailAddress)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabled(string $emailAddress): ?UserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.emailAddress = :emailAddress')
            ->andWhere('u.enabled = TRUE')
            ->setParameter('emailAddress', $emailAddress)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByRegistrationToken(string $token): ?EditableUserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.token = :token')
            ->andWhere('u.tokenExpirationDate >= :now')
            ->andWhere('u.password IS NULL')
            ->andWhere('u.enabled = TRUE')
            ->setParameter('token', $token)
            ->setParameter('now', new DateTime())
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByPasswordChangeToken(string $token): ?EditableUserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.token = :token')
            ->andWhere('u.tokenExpirationDate >= :now')
            ->andWhere('u.password IS NOT NULL')
            ->andWhere('u.enabled = TRUE')
            ->setParameter('token', $token)
            ->setParameter('now', new DateTime())
            ->getQuery()->getOneOrNullResult();
    }
}
