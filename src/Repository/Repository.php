<?php

namespace Vinorcola\PrivateUserBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class Repository
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Return the class of the entity handled by the repository.
     *
     * @return string
     */
    abstract public static function getEntityClass(): string;

    /**
     * Repository constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Create a query builder.
     *
     * @param string $alias
     * @return QueryBuilder
     */
    protected function createQueryBuilder(string $alias): QueryBuilder
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select($alias)
            ->from(static::getEntityClass(), $alias);
    }

    /**
     * Get a reference of an entity.
     *
     * @param string $entityClass
     * @param string $id
     * @return object
     */
    protected function getReference(string $entityClass, string $id): object
    {
        return $this->entityManager->getReference($entityClass, $id);
    }
}
