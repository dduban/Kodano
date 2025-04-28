<?php

declare(strict_types=1);

namespace Repository;

use Category;
use Ramsey\Uuid\UuidInterface;
use ValueObject\CategoryCode;

interface CategoryRepositoryInterface
{
    public function findById(UuidInterface $id): ?Category;

    public function findByCode(CategoryCode $code): ?Category;

    public function findAll(): array;

    public function save(Category $category): void;

    public function remove(Category $category): void;

    public function isCodeUnique(CategoryCode $code): bool;
}