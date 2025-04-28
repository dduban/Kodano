<?php

declare(strict_types=1);

namespace App\Domain\Exception\Product;

class ProductNotFoundException extends \DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Product with id "%s" not found', $id));
    }
}