<?php

namespace App\Infrastructure\Repositories;

use App\Application\DTO\Reservation\CreateReservationDTO;
use App\Application\DTO\Reservation\UpdateReservationDTO;
use App\Domain\Models\Customer;
use App\Domain\Models\Reservation;
use App\Domain\Repositories\ReservationRepositoryInterface;
use Carbon\Carbon;

class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function overlapExists(Carbon $startAt, Carbon $endAt, ?int $ignoreReservationId = null): bool
    {
        $query = Reservation::query()
            ->where('status', '!=', 'cancel')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        if ($ignoreReservationId !== null) {
            $query->where('id', '!=', $ignoreReservationId);
        }

        return $query->exists();
    }

    public function createDraftReservation(CreateReservationDTO $dto): int
    {
        $customerId = null;
        if ($dto->customerName !== null) {
            $customerId = Customer::query()->create([
                'name' => $dto->customerName,
                'phone' => '',
                'memo' => null,
            ])->id;
        }

        return Reservation::query()->create([
            'start_at' => $dto->startAt,
            'end_at' => $dto->endAt,
            'customer_id' => $customerId,
            'status' => 'booked',
            'memo' => null,
        ])->id;
    }

    public function updateReservation(int $reservationId, UpdateReservationDTO $dto): void
    {
        $reservation = Reservation::query()->findOrFail($reservationId);

        $customerId = $dto->customerId;

        if ($customerId !== null) {
            $customer = Customer::query()->find($customerId);
            if ($customer !== null) {
                $customer->update([
                    'name' => $dto->customerName ?? $customer->name,
                    'phone' => $dto->customerPhone ?? '',
                ]);
            }
        } elseif ($dto->customerName !== null) {
            $customerId = Customer::query()->create([
                'name' => $dto->customerName,
                'phone' => $dto->customerPhone ?? '',
                'memo' => null,
            ])->id;
        }

        if ($dto->customerName === null && $customerId === null) {
            $reservation->customer_id = null;
        } else {
            $reservation->customer_id = $customerId;
        }

        $reservation->memo = $dto->memo;
        $reservation->status = $dto->status;
        $reservation->save();
    }

    public function cancelReservation(int $reservationId): void
    {
        Reservation::query()->whereKey($reservationId)->update([
            'status' => 'cancel',
        ]);
    }
}

