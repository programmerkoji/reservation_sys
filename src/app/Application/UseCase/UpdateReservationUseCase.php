<?php

namespace App\Application\UseCase;

use App\Application\DTO\Reservation\UpdateReservationDTO;
use App\Domain\Repositories\ReservationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateReservationUseCase
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function execute(int $reservationId, UpdateReservationDTO $dto): void
    {
        DB::transaction(function () use ($reservationId, $dto): void {
            $this->reservations->updateReservation($reservationId, $dto);
        });
    }
}

