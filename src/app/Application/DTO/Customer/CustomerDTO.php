<?php

namespace App\Application\DTO\Customer;

class CustomerDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $memo,
    ) {
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int) $input['id'],
            (string) $input['name'],
            (string) ($input['phone'] ?? ''),
            $input['memo'] ?? null,
        );
    }
}

