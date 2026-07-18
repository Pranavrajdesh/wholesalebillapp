<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Services\OtpService;
use Illuminate\Http\Request;

class RetailerAuthController extends Controller
{
    public function requestOtp(Request $request, OtpService $otpService)
    {
        $data = $request->validate([
            'mobile' => ['required', 'digits:10'],
        ]);

        $partner = $this->activePartner($data['mobile']);

        if (!$partner) {
            return response()->json([
                'ok' => false,
                'message' => 'This mobile number is not registered with us. Please contact the wholesaler.',
            ], 404);
        }

        $otp = $otpService->send($partner->mobile);

        return response()->json([
            'ok' => true,
            'message' => 'OTP sent.',
            // Dev mode: exposed until the SMS gateway is wired. Remove then.
            'dev_otp' => $otp->code,
        ]);
    }

    public function verifyOtp(Request $request, OtpService $otpService)
    {
        $data = $request->validate([
            'mobile' => ['required', 'digits:10'],
            'code' => ['required', 'digits:6'],
        ]);

        $partner = $this->activePartner($data['mobile']);

        if (!$partner) {
            return response()->json(['ok' => false, 'message' => 'Not registered.'], 404);
        }

        if (!$otpService->verify($data['mobile'], $data['code'])) {
            return response()->json(['ok' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        $token = $partner->createToken('retailer')->plainTextToken;

        return response()->json([
            'ok' => true,
            'token' => $token,
            'partner' => $this->profile($partner),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['ok' => true, 'partner' => $this->profile($request->user())]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    private function activePartner(string $mobile): ?Partner
    {
        return Partner::where('mobile', $mobile)
            ->where('is_active', true)
            ->where('portal_access', true)
            ->first();
    }

    private function profile(Partner $partner): array
    {
        return [
            'id' => $partner->id,
            'firm_name' => $partner->firm_name,
            'contact_name' => $partner->contact_name,
            'mobile' => $partner->mobile,
            'show_prices' => $partner->show_prices,
        ];
    }
}
