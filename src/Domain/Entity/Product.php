<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Infrastructure\Controller\NotificationController;
use App\Infrastructure\Controller\ProductController;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ApiResource(
    operations: [
        new Get(
            controller: ProductController::class,
            normalizationContext: ['groups' => ['product:read']],
            name: 'get_product'
        ),
        new GetCollection(
            controller: ProductController::class,
            normalizationContext: ['groups' => ['product:read']],
            name: 'get_products_collection'
        ),
        new Post(
            controller: ProductController::class,
            denormalizationContext: ['groups' => ['product:write']],
            name: 'create_product'
        ),
        new Put(
            controller: ProductController::class,
            denormalizationContext: ['groups' => ['product:write']],
            name: 'update_product'
        ),
        new Patch(
            controller: ProductController::class,
            denormalizationContext: ['groups' => ['product:write']],
            name: 'patch_product'
        ),
        new Delete(
            controller: ProductController::class,
            name: 'delete_product'
        ),
        new Post(
            uriTemplate: '/products/{id}/notify',
            controller: NotificationController::class,
            openapiContext: [
                'summary' => 'Sends notifications for a specific product.',
                'description' => 'Triggers the notification sending process for the product identified by ID.',
                'requestBody' => ['content' => []],
                'responses' => [
                    '200' => ['description' => 'Notifications sent successfully'],
                    '404' => ['description' => 'Product not found'],
                ],
                'tags' => ['Product'],
            ],
            read: false,
            validate: false,
            write: false,
            name: 'notify_product'
        ),
        new Post(
            uriTemplate: '/products/{id}/categories/{categoryId}',
            controller: ProductController::class,
            openapiContext: [
                'summary' => 'Adds a category to a product.',
                'description' => 'Associates the specified category with the product.',
                'requestBody' => ['content' => []],
                'responses' => [
                    '200' => ['description' => 'Category added successfully'],
                    '404' => ['description' => 'Product or category not found'],
                ],
                'tags' => ['Product'],
            ],
            normalizationContext: ['groups' => ['product:read']],
            read: false,
            validate: false,
            write: false,
            name: 'add_category_to_product'
        ),
        new Delete(
            uriTemplate: '/products/{id}/categories/{categoryId}',
            controller: ProductController::class,
            openapiContext: [
                'summary' => 'Removes a category from a product.',
                'description' => 'Disassociates the specified category from the product.',
                'responses' => [
                    '200' => ['description' => 'Category removed successfully'],
                    '404' => ['description' => 'Product or category not found'],
                    '400' => ['description' => 'Validation error (e.g., product must have at least one category)'],
                ],
                'tags' => ['Product'],
            ],
            normalizationContext: ['groups' => ['product:read']],
            read: false,
            validate: false,
            write: false,
            name: 'remove_category_from_product'
        )
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['product:read', 'category:read'])]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product:read', 'product:write', 'category:read'])]
    private string $name;

    #[ORM\Column(type: 'float')]
    #[Groups(['product:read', 'product:write', 'category:read'])]
    private float $price;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['product:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['product:read'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'product_categories')]
    #[Groups(['product:read', 'product:write'])]
    private Collection $categories;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param float $price
     */
    public function __construct(UuidInterface $id, string $name, float $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->setPrice($price);
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return void
     */
    private function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new DomainException('Price cannot be negative');
        }

        $this->price = $price;
    }

    /**
     * @param float $price
     * @return void
     */
    public function updatePrice(float $price): void
    {
        $this->setPrice($price);
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * @return void
     */
    public function addCategory(Category $category): void
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @param Category $category
     * @return void
     */
    public function removeCategory(Category $category): void
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function belongsToCategory(Category $category): bool
    {
        return $this->categories->contains($category);
    }

    /**
     * @param string $name
     * @param float $price
     * @return void
     */
    public function update(string $name, float $price): void
    {
        $this->setName($name);
        $this->updatePrice($price);
    }
}