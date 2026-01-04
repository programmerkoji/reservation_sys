<?php

namespace App\Domain\Repositories;

use App\Application\DTO\Reservation\CreateReservationDTO;
use App\Application\DTO\Reservation\UpdateReservationDTO;
use Carbon\Carbon;

interface ReservationRepositoryInterface
{
    public function overlapExists(Carbon $startAt, Carbon $endAt, ?int $ignoreReservationId = null): bool;

    public function createDraftReservation(CreateReservationDTO $dto): int;

    public function updateReservation(int $reservationId, UpdateReservationDTO $dto): void;

    public function cancelReservation(int $reservationId): void;
}

