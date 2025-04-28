<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Product;
use Ramsey\Uuid\UuidInterface;

interface ProductRepositoryInterface
{
    public function findById(UuidInterface $id): ?Product;

    public function findAll(): array;

    public function save(Product $product): void;

    public function remove(Product $product): void;
}