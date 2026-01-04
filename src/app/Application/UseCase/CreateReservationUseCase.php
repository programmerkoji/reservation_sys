<?php

namespace App\Application\UseCase;

use App\Application\DTO\Reservation\CreateReservationDTO;
use App\Domain\Repositories\ReservationRepositoryInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CreateReservationUseCase
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function execute(CreateReservationDTO $dto): int
    {
        $setting = Setting::singleton();

        $diffSeconds = $dto->endAt->getTimestamp() - $dto->startAt->getTimestamp();
        if ($diffSeconds !== (int) $setting->slot_minutes * 60) {
            throw new InvalidArgumentException('予約時間が正しくありません。');
        }

        return DB::transaction(function () use ($dto): int {
            if ($this->reservations->overlapExists($dto->startAt, $dto->endAt)) {
                throw new InvalidArgumentException('その時間はすでに予約があります。');
            }

            return $this->reservations->createDraftReservation($dto);
        });
    }
}
