<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use DateTime;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;
use Vinorcola\PrivateUserBundle\Model\UserInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getEntityClass(): string
    {
        return User::class;
    }

    /**
     * {@inheritDoc}
     */
    public function add(EditableUserInterface $user): void
    {
        $this->entityManager->persist($user);
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(EditableUserInterface $user): void
    {
        $this->entityManager->persist($user);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
