<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Validation;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class MessageValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(object $message): void
    {
        $violations = $this->validator->validate($message);

        if (count($violations) > 0) {
            throw new ValidationFailedException($message, $violations);
        }
    }
}
