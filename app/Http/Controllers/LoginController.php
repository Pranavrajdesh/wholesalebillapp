<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('products.index');
        }

        if (Auth::guard('partner')->check()) {
            return redirect()->route('retailer.home');
        }

        return view('auth.login');
    }

    public function sendOtp(Request $request, OtpService $otpService)
    {
        $data = $request->validate([
            'mobile' => ['required', 'digits:10'],
        ]);

        $mobile = $data['mobile'];

        $account = $this->accountFor($mobile);
        if (!$account) {
            return back()
                ->withErrors(['mobile' => 'This mobile number is not registered. Contact your wholesaler.'])
                ->withInput();
        }
        if ($account instanceof Partner) {
            return back()
                ->with('retailer_hint', true)
                ->withInput();
        }

        $otp = $otpService->send($mobile);
        session()->put('dev_otp_' . $mobile, $otp->code); // dev mode only; remove with SMS gateway
        return view('auth.login', [
            'step' => 'verify',
            'mobile' => $mobile,
        ]);
    }

    public function verify(Request $request, OtpService $otpService)
    {
        $mobile = (string) $request->input('mobile', '');
        $code = (string) $request->input('code', '');
        if (!preg_match('/^\d{10}$/', $mobile)) {
            return redirect()->route('login');
        }
        if (!preg_match('/^\d{6}$/', $code)) {
            return view('auth.login', [
                'step' => 'verify',
                'mobile' => $mobile,
                'error' => 'Please enter the 6-digit OTP.',
            ]);
        }
        $data = ['mobile' => $mobile, 'code' => $code];

        if (!$otpService->verify($data['mobile'], $data['code'])) {
            return view('auth.login', [
                'step' => 'verify',
                'mobile' => $data['mobile'],
                'error' => 'Invalid or expired OTP. Go back and request a new one if needed.',
            ]);
        }

        $account = $this->accountFor($data['mobile']);

        if ($account instanceof User) {
            Auth::guard('web')->login($account);
            $request->session()->regenerate();

            return redirect()->route('products.index');
        }

        if ($account instanceof Partner) {
            Auth::guard('partner')->login($account);
            $request->session()->regenerate();

            return redirect()->route('retailer.home');
        }

        return redirect()->route('login')->withErrors(['mobile' => 'Account not found.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('partner')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function accountFor(string $mobile): User|Partner|null
    {
        $user = User::where('mobile', $mobile)->first();

        if ($user) {
            return $user; // owner wins if a mobile exists in both tables
        }

        return Partner::where('mobile', $mobile)
            ->where('is_active', true)
            ->where('portal_access', true)
            ->first();
    }
}
