<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('settings.edit', ['s' => Setting::getAll()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'firm_name' => ['required', 'string', 'max:255'],
            'firm_gst' => ['nullable', 'string', 'max:15'],
            'firm_mobile' => ['required', 'digits:10'],
            'firm_alt_mobile' => ['nullable', 'digits:10'],
            'firm_address' => ['nullable', 'string', 'max:500'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account' => ['nullable', 'string', 'max:30'],
            'bank_ifsc' => ['nullable', 'string', 'max:15'],
            'bank_holder' => ['nullable', 'string', 'max:100'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'print_payment' => ['required', 'in:0,1'],
            'print_projection' => ['required', 'in:0,1'],
            'allow_negative_stock' => ['required', 'in:0,1'],
            'composition_rate' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ]);

        Setting::setMany($data);

        return redirect()->route('settings.edit')->with('status', 'Settings saved.');
    }
}