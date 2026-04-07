<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['interest_rate', 'commodity_rate'])->get()
            ->pluck('value', 'key');

        return response()->json(['success' => true, 'data' => $settings]);
    }
}
