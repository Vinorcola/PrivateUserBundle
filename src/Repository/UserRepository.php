<?php

namespace Vinorcola\PrivateUserBundle\Repository;

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
    public function find(string $emailAddress): ?EditableUserInterface
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.emailAddress = :emailAddress')
            ->setParameter('emailAddress', $emailAddress)
            ->getQuery()->getOneOrNullResult();
    }
}
