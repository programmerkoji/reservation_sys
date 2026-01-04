<?php

namespace App\Http\Controllers;

use App\Domain\Models\Customer;
use App\Models\Setting;

class CalendarController extends Controller
{
    public function index()
    {
        $setting = Setting::singleton();

        return view('dashboard.calendar', [
            'setting' => $setting,
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }
}
