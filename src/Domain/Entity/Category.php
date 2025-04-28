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
use App\Domain\ValueObject\CategoryCode;
use App\Infrastructure\Controller\CategoryController;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ApiResource(
    operations: [
        new Get(
            controller: CategoryController::class,
            normalizationContext: ['groups' => ['category:read']],
            name: 'get_category'
        ),
        new GetCollection(
            controller: CategoryController::class,
            normalizationContext: ['groups' => ['category:read']],
            name: 'get_categories_collection'
        ),
        new Post(
            controller: CategoryController::class,
            denormalizationContext: ['groups' => ['category:write']],
            name: 'create_category'
        ),
        new Put(
            controller: CategoryController::class,
            denormalizationContext: ['groups' => ['category:write']],
            name: 'update_category'
        ),
        new Patch(
            controller: CategoryController::class,
            denormalizationContext: ['groups' => ['category:write']],
            name: 'patch_category'
        ),
        new Delete(
            controller: CategoryController::class,
            name: 'delete_category'
        ),
    ]
)]
class Category
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    #[Groups(['category:read', 'product:read'])]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    #[Groups(['category:read', 'category:write', 'product:read'])]
    private string $code;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    #[Groups(['category:read'])]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    #[Groups(['category:read'])]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'categories')]
    #[Groups(['category:read'])]
    private Collection $products;

    /**
     * @param UuidInterface $id
     * @param CategoryCode $code
     */
    public function __construct(UuidInterface $id, CategoryCode $code)
    {
        $this->id = $id;
        $this->code = (string)$code;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->products = new ArrayCollection();
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
    #[Groups(['category:read', 'category:write', 'product:read'])]
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return CategoryCode
     */
    #[Ignore]
    public function getCategoryCode(): CategoryCode
    {
        return new CategoryCode($this->code);
    }

    /**
     * @param CategoryCode $code
     * @return void
     */
    public function updateCode(CategoryCode $code): void
    {
        $this->code = (string)$code;
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
    public function getProducts(): Collection
    {
        return $this->products;
    }
}