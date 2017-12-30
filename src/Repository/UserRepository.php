<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use Vinorcola\PrivateUserBundle\Entity\User;
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
    public function add(UserInterface $user): void
    {
        $this->entityManager->persist($user);
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
}
