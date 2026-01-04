<?php

namespace App\Http\Controllers;

use App\Application\DTO\Reservation\CreateReservationDTO;
use App\Application\DTO\Reservation\UpdateReservationDTO;
use App\Application\UseCase\CreateReservationUseCase;
use App\Application\UseCase\DeleteReservationUseCase;
use App\Application\UseCase\UpdateReservationUseCase;
use App\Domain\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;

class ReservationController extends Controller
{
    public function __construct(
        private readonly CreateReservationUseCase $createReservation,
        private readonly UpdateReservationUseCase $updateReservation,
        private readonly DeleteReservationUseCase $deleteReservation,
    ) {
    }

    public function index(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        $start = Carbon::parse($request->string('start'));
        $end = Carbon::parse($request->string('end'));

        $reservations = Reservation::query()
            ->with('customer:id,name')
            ->where('status', '!=', 'cancel')
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->orderBy('start_at')
            ->get();

        return response()->json($reservations->map(function (Reservation $reservation) {
            $title = $reservation->start_at->format('H:i').'-'.$reservation->end_at->format('H:i');

            if ($reservation->customer?->name !== null && $reservation->customer->name !== '') {
                $title .= "\n".$reservation->customer->name;
            }

            return [
                'id' => (string) $reservation->id,
                'title' => $title,
                'start' => $reservation->start_at->toIso8601String(),
                'end' => $reservation->end_at->toIso8601String(),
            ];
        }));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('customer:id,name,phone');

        return response()->json([
            'id' => $reservation->id,
            'start_at' => $reservation->start_at->toIso8601String(),
            'end_at' => $reservation->end_at->toIso8601String(),
            'status' => $reservation->status,
            'memo' => $reservation->memo,
            'customer' => $reservation->customer ? [
                'id' => $reservation->customer->id,
                'name' => $reservation->customer->name,
                'phone' => $reservation->customer->phone,
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $dto = CreateReservationDTO::fromArray($request->all());

        try {
            $reservationId = $this->createReservation->execute($dto);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['id' => $reservationId], Response::HTTP_CREATED);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $dto = UpdateReservationDTO::fromArray($request->all());
        $this->updateReservation->execute((int) $reservation->id, $dto);

        return response()->noContent();
    }

    public function destroy(Reservation $reservation)
    {
        $this->deleteReservation->execute((int) $reservation->id);

        return response()->noContent();
    }
}
