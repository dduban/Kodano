<?php

namespace App\Domain\Exception\Category;

class CategoryNotFoundException extends \DomainException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('Category with id "%s" not found', $id));
    }
}