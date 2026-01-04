<?php

namespace App\Application\DTO\Reservation;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateReservationDTO
{
    public function __construct(
        public readonly Carbon $startAt,
        public readonly Carbon $endAt,
        public readonly ?string $customerName,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public static function fromArray(array $input): self
    {
        $validated = Validator::make($input, [
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'customer_name' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $customerName = trim((string) ($validated['customer_name'] ?? '')) ?: null;

        return new self(
            Carbon::parse($validated['start_at']),
            Carbon::parse($validated['end_at']),
            $customerName,
        );
    }
}
