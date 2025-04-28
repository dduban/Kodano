<?php

namespace App\Domain\Exception\Category;

class CategoryCodeNotUniqueException extends \DomainException
{
    public function __construct(string $code)
    {
        parent::__construct(sprintf('Category with code "%s" already exists', $code));
    }
}