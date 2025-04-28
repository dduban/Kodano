<?php

declare(strict_types=1);

namespace ValueObject;

use InvalidArgumentException;

final class CategoryCode
{
    private const MAX_LENGTH = 10;
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    /**
     * @param string $code
     * @return void
     */
    private function validate(string $code): void
    {
        if (empty($code)) {
            throw new InvalidArgumentException('Category code cannot be empty');
        }

        if (strlen($code) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Category code cannot be longer than %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}