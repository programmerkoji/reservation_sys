<?php

namespace App\Application\UseCase;

use App\Domain\Repositories\ReservationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteReservationUseCase
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function execute(int $reservationId): void
    {
        DB::transaction(function () use ($reservationId): void {
            $this->reservations->cancelReservation($reservationId);
        });
    }
}

