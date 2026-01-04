<?php

namespace App\Application\DTO\Reservation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateReservationDTO
{
    public function __construct(
        public readonly ?int $customerId,
        public readonly ?string $customerName,
        public readonly ?string $customerPhone,
        public readonly ?string $memo,
        public readonly string $status,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public static function fromArray(array $input): self
    {
        $validated = Validator::make($input, [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['booked', 'done', 'cancel'])],
        ])->validate();

        $customerName = trim((string) ($validated['customer_name'] ?? '')) ?: null;

        $customerPhone = trim((string) ($validated['customer_phone'] ?? '')) ?: null;

        return new self(
            $validated['customer_id'] ?? null,
            $customerName,
            $customerPhone,
            $validated['memo'] ?? null,
            $validated['status'],
        );
    }
}
