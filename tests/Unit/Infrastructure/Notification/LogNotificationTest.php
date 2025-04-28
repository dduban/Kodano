<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Notification;

use App\Domain\Entity\Product;
use App\Infrastructure\Notification\LogNotification;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class LogNotificationTest extends TestCase
{
    public function testSend(): void
    {
        // Arrange
        $logger = $this->createMock(LoggerInterface::class);
        $notification = new LogNotification($logger);

        $product = new Product(Uuid::uuid4(), 'Test Product', 10.99);

        // Assert
        $logger->expects($this->once())
            ->method('info')
            ->with(
                $this->equalTo('Product saved'),
                $this->callback(function (array $context) use ($product) {
                    return isset($context['id'])
                        && isset($context['name'])
                        && isset($context['price'])
                        && $context['name'] === $product->getName()
                        && $context['price'] === $product->getPrice();
                })
            );

        // Act
        $notification->send($product);
    }
}
