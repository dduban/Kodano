<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Entity\Product;
use App\Domain\Service\NotificationInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotification implements NotificationInterface
{
    private MailerInterface $mailer;
    private string $senderEmail;

    /**
     * @param MailerInterface $mailer
     * @param string $senderEmail
     */
    public function __construct(MailerInterface $mailer, string $senderEmail = 'noreply@example.com')
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }

    /**
     * @param Product $product
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(Product $product): void
    {
        $email = (new Email())
            ->from($this->senderEmail)
            ->to('admin@example.com')
            ->subject('New product saved: ' . $product->getName())
            ->html(
                sprintf(
                    '<p>New product has been saved:</p>
                    <ul>
                        <li>ID: %s</li>
                        <li>Name: %s</li>
                        <li>Price: %.2f</li>
                        <li>Categories: %s</li>
                    </ul>',
                    $product->getId()->toString(),
                    $product->getName(),
                    $product->getPrice(),
                    implode(', ', array_map(
                        fn($category) => $category->getCode()->getValue(),
                        $product->getCategories()->toArray()
                    ))
                )
            );

        $this->mailer->send($email);
    }
}
