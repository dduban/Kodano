<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Category;
use App\Domain\Exception\Category\CategoryCodeNotUniqueException;
use App\Domain\Exception\Category\CategoryNotFoundException;
use App\Domain\Exception\Category\CategoryValidationException;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\ValueObject\CategoryCode;
use Ramsey\Uuid\Uuid;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @param string $id
     * @return Category
     */
    public function findById(string $id): Category
    {
        $uuid = Uuid::fromString($id);
        $category = $this->categoryRepository->findById($uuid);

        if (!$category) {
            throw new CategoryNotFoundException($id);
        }

        return $category;
    }

    /**
     * @param string $code
     * @return Category
     */
    public function create(string $code): Category
    {
        if (empty($code)) {
            throw new CategoryValidationException('Code is required');
        }

        try {
            $categoryCode = new CategoryCode($code);
        } catch (\InvalidArgumentException $e) {
            throw new CategoryValidationException($e->getMessage());
        }

        if (!$this->categoryRepository->isCodeUnique($categoryCode)) {
            throw new CategoryCodeNotUniqueException($code);
        }

        $category = new Category(Uuid::uuid4(), $categoryCode);
        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * @param string $id
     * @param string $code
     * @return Category
     */
    public function update(string $id, string $code): Category
    {
        $category = $this->findById($id);

        if (empty($code)) {
            throw new CategoryValidationException('Code is required');
        }

        try {
            $categoryCode = new CategoryCode($code);
        } catch (\InvalidArgumentException $e) {
            throw new CategoryValidationException($e->getMessage());
        }

        $existingCategory = $this->categoryRepository->findByCode($categoryCode);
        if ($existingCategory && !$existingCategory->getId()->equals($category->getId())) {
            throw new CategoryCodeNotUniqueException($code);
        }

        $category->updateCode($categoryCode);
        $this->categoryRepository->save($category);

        return $category;
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $category = $this->findById($id);
        $this->categoryRepository->remove($category);
    }
}