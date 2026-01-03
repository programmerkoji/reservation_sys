<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('dashboard.settings.edit', [
            'setting' => Setting::singleton(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'open_weekdays' => ['required', 'array'],
            'open_weekdays.*' => ['integer', 'between:0,6'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
        ]);

        $setting = Setting::singleton();
        $openWeekdays = array_values(array_unique(array_map('intval', $validated['open_weekdays'])));
        sort($openWeekdays);

        $setting->update([
            'open_weekdays' => $openWeekdays,
            'open_time' => $validated['open_time'],
            'close_time' => $validated['close_time'],
        ]);

        return redirect()->route('dashboard.settings.edit');
    }
}

