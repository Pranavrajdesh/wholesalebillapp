<?php

namespace App\Services;

use App\Models\OtpCode;

class OtpService
{
    public function send(string $mobile): OtpCode
    {
        // Invalidate any previous unused codes for this mobile
        OtpCode::where('mobile', $mobile)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = OtpCode::create([
            'mobile' => $mobile,
            'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addMinutes(5),
        ]);

        // SMS gateway hook goes here later; dev mode shows the code on screen.

        return $otp;
    }

    public function verify(string $mobile, string $code): bool
    {
        $otp = OtpCode::where('mobile', $mobile)
            ->where('code', $code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (!$otp) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }
}