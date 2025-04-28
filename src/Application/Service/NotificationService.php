<?php

declare(strict_types=1);


use App\Domain\Entity\Product;
use App\Domain\Service\NotificationInterface;

class NotificationService
{
    /**
     * @var array<NotificationInterface>
     */
    private array $notifications = [];

    public function __construct(iterable $notifications = [])
    {
        foreach ($notifications as $notification) {
            $this->addNotification($notification);
        }
    }

    public function addNotification(NotificationInterface $notification): void
    {
        $this->notifications[] = $notification;
    }

    public function sendAll(Product $product): void
    {
        foreach ($this->notifications as $notification) {
            $notification->send($product);
        }
    }
}