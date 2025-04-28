<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Category;
use App\Domain\ValueObject\CategoryCode;
use Ramsey\Uuid\UuidInterface;

interface CategoryRepositoryInterface
{
    public function findById(UuidInterface $id): ?Category;

    public function findByCode(CategoryCode $code): ?Category;

    public function findAll(): array;

    public function save(Category $category): void;

    public function remove(Category $category): void;

    public function isCodeUnique(CategoryCode $code): bool;
}