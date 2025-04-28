<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\NotificationService;
use App\Domain\Entity\Product;
use App\Domain\Service\NotificationInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class NotificationServiceTest extends TestCase
{
    public function testSendAllCallsAllRegisteredNotifications(): void
    {
        // Arrange
        $notification1 = $this->createMock(NotificationInterface::class);
        $notification2 = $this->createMock(NotificationInterface::class);

        $notificationService = new NotificationService([$notification1, $notification2]);

        $product = new Product(Uuid::uuid4(), 'Test Product', 10.99);

        // Assert
        $notification1->expects($this->once())
            ->method('send')
            ->with($product);

        $notification2->expects($this->once())
            ->method('send')
            ->with($product);

        // Act
        $notificationService->sendAll($product);
    }

    public function testAddNotification(): void
    {
        // Arrange
        $notificationService = new NotificationService();
        $notification = $this->createMock(NotificationInterface::class);

        $product = new Product(Uuid::uuid4(), 'Test Product', 10.99);

        // Assert
        $notification->expects($this->once())
            ->method('send')
            ->with($product);

        // Act
        $notificationService->addNotification($notification);
        $notificationService->sendAll($product);
    }
}