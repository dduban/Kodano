<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Category;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\ValueObject\CategoryCode;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class DoctrineCategoryRepository implements CategoryRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UuidInterface $id
     * @return Category|null
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findById(UuidInterface $id): ?Category
    {
        return $this->entityManager->find(Category::class, $id);
    }

    /**
     * @param CategoryCode $code
     * @return Category|null
     */
    public function findByCode(CategoryCode $code): ?Category
    {
        return $this->entityManager->getRepository(Category::class)
            ->findOneBy(['code' => (string)$code]);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    /**
     * @param Category $category
     * @return void
     */
    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     * @return void
     */
    public function remove(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    /**
     * @param CategoryCode $code
     * @return bool
     */
    public function isCodeUnique(CategoryCode $code): bool
    {
        return null === $this->findByCode($code);
    }
}