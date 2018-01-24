<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use DateTime;
use Vinorcola\PrivateUserBundle\Entity\User;
use Vinorcola\PrivateUserBundle\Model\EditableUserInterface;

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
    public function find(string $emailAddress): ?UserInterface
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
    public function findByRegistrationToken(string $token): ?EditableUserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.token = :token')
            ->andWhere('u.tokenExpirationDate >= :now')
            ->andWhere('u.password IS NULL')
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
            ->setParameter('token', $token)
            ->setParameter('now', new DateTime())
            ->getQuery()->getOneOrNullResult();
    }
}
