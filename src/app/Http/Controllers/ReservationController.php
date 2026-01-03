<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
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
        $setting = Setting::singleton();

        $validated = $request->validate([
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'customer_name' => ['nullable', 'string', 'max:255'],
        ]);

        $startAt = Carbon::parse($validated['start_at']);
        $endAt = Carbon::parse($validated['end_at']);

        $diffSeconds = $endAt->getTimestamp() - $startAt->getTimestamp();
        if ($diffSeconds !== (int) $setting->slot_minutes * 60) {
            return response()->json([
                'message' => '予約時間が正しくありません。',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $overlapExists = Reservation::query()
            ->where('status', '!=', 'cancel')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($overlapExists) {
            return response()->json([
                'message' => 'その時間はすでに予約があります。',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customerId = null;
        $customerName = trim((string) ($validated['customer_name'] ?? ''));
        if ($customerName !== '') {
            $customerId = Customer::query()->create([
                'name' => $customerName,
                'phone' => '',
                'memo' => null,
            ])->id;
        }

        $reservation = Reservation::query()->create([
            'start_at' => $startAt,
            'end_at' => $endAt,
            'customer_id' => $customerId,
            'status' => 'booked',
            'memo' => null,
        ]);

        return response()->json([
            'id' => $reservation->id,
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['booked', 'done', 'cancel'])],
        ]);

        $customerId = $validated['customer_id'] ?? null;
        $customerName = trim((string) ($validated['customer_name'] ?? ''));
        $customerPhone = trim((string) ($validated['customer_phone'] ?? ''));

        if ($customerId !== null) {
            $customer = Customer::query()->find($customerId);
            if ($customer !== null) {
                $customer->update([
                    'name' => $customerName !== '' ? $customerName : $customer->name,
                    'phone' => $customerPhone,
                ]);
            }
        } elseif ($customerName !== '') {
            $customerId = Customer::query()->create([
                'name' => $customerName,
                'phone' => $customerPhone,
                'memo' => null,
            ])->id;
        }

        if ($customerName === '' && $customerId === null) {
            $reservation->customer_id = null;
        } else {
            $reservation->customer_id = $customerId;
        }

        $reservation->memo = $validated['memo'] ?? null;
        $reservation->status = $validated['status'];
        $reservation->save();

        return response()->noContent();
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->update([
            'status' => 'cancel',
        ]);

        return response()->noContent();
    }
}
