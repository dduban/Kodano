<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Notification;

use App\Domain\Entity\Product;
use App\Infrastructure\Notification\EmailNotification;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationTest extends TestCase
{
    public function testSend(): void
    {
        // Arrange
        $mailer = $this->createMock(MailerInterface::class);
        $notification = new EmailNotification($mailer);

        $product = new Product(Uuid::uuid4(), 'Test Product', 10.99);

        // Assert
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($product) {
                return strpos($email->getSubject(), $product->getName()) !== false
                    && strpos($email->getHtmlBody(), $product->getName()) !== false
                    && strpos($email->getHtmlBody(), (string)$product->getPrice()) !== false;
            }));

        // Act
        $notification->send($product);
    }
}