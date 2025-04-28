<?php
declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Product;
use App\Domain\Exception\Category\CategoryNotFoundException;
use App\Domain\Exception\Product\ProductValidationException;
use App\Domain\Exception\Product\ProductNotFoundException;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\ProductRepositoryInterface;
use NotificationService;
use Ramsey\Uuid\Uuid;

readonly class ProductService
{
    public function __construct(
        private ProductRepositoryInterface  $productRepository,
        private CategoryRepositoryInterface $categoryRepository,
        private NotificationService         $notificationService
    ) {
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param string $id
     * @return Product
     */
    public function findById(string $id): Product
    {
        $uuid = Uuid::fromString($id);
        $product = $this->productRepository->findById($uuid);

        if (!$product) {
            throw new ProductNotFoundException($id);
        }

        return $product;
    }

    /**
     * @param string $name
     * @param float $price
     * @param array $categoryIds
     * @return Product
     */
    public function create(string $name, float $price, array $categoryIds): Product
    {
        if (empty($name)) {
            throw new ProductValidationException('Name is required');
        }

        if (empty($categoryIds)) {
            throw new ProductValidationException('Product must belong to at least one category');
        }

        $product = new Product(Uuid::uuid4(), $name, $price);

        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->findById(Uuid::fromString($categoryId));
            if (!$category) {
                throw new CategoryNotFoundException($categoryId);
            }
            $product->addCategory($category);
        }

        $this->productRepository->save($product);
        $this->notificationService->sendAll($product);

        return $product;
    }

    /**
     * @param string $id
     * @param string $name
     * @param float $price
     * @param array|null $categoryIds
     * @return Product
     */
    public function update(string $id, string $name, float $price, ?array $categoryIds = null): Product
    {
        $product = $this->findById($id);

        if (empty($name)) {
            throw new ProductValidationException('Name is required');
        }

        $product->update($name, $price);

        if ($categoryIds !== null) {
            if (empty($categoryIds)) {
                throw new ProductValidationException('Product must belong to at least one category');
            }

            foreach ($product->getCategories()->toArray() as $category) {
                $product->removeCategory($category);
            }

            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->findById(Uuid::fromString($categoryId));
                if (!$category) {
                    throw new CategoryNotFoundException($categoryId);
                }
                $product->addCategory($category);
            }
        }

        $this->productRepository->save($product);
        $this->notificationService->sendAll($product);

        return $product;
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $product = $this->findById($id);
        $this->productRepository->remove($product);
    }

    /**
     * @param string $id
     * @return void
     */
    public function sendNotifications(string $id): void
    {
        $product = $this->findById($id);
        $this->notificationService->sendAll($product);
    }
}