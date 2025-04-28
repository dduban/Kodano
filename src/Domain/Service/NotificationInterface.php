<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;

interface NotificationInterface
{
    public function send(Product $product): void;
}