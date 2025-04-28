<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Entity\Product;
use App\Domain\Service\NotificationInterface;
use Psr\Log\LoggerInterface;

class LogNotification implements NotificationInterface
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Product $product
     * @return void
     */
    public function send(Product $product): void
    {
        $this->logger->info('Product saved', [
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'categories' => array_map(
                fn($category) => $category->getCode()->getValue(),
                $product->getCategories()->toArray()
            )
        ]);
    }
}
