@extends('layouts.app')

@section('title', 'Wholesaler Login')

@section('content')
    <h2>Wholesaler Login</h2>
    <hr class="rule">

    @if (session('retailer_hint'))
        <div style="margin:12px 0; font-size:14px; font-weight:600; color:#1a1a1a; padding:10px 12px; border:1px solid #999; border-left:4px solid #1a1a1a; background:#f7f7f7; border-radius:4px;">
            This number is registered as a <b>retailer</b>. Please order from the retailer portal:
            <div style="margin-top:8px;"><a class="btn" href="{{ route('retailer.home') }}" style="text-decoration:none;">GO TO RETAILER PORTAL &rarr;</a></div>
        </div>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    @if (!empty($error))
        <div class="error">{{ $error }}</div>
    @endif

    @if (($step ?? 'mobile') === 'verify')
        <div class="card">
            <div class="muted">OTP sent to</div>
            <div style="font-weight:700; font-size:18px;">{{ $mobile }}</div>
        </div>

        @if (session('dev_otp_' . $mobile))
            <div class="status">DEV MODE &mdash; your OTP is <b style="font-size:18px; letter-spacing:2px;">{{ session('dev_otp_' . $mobile) }}</b></div>
        @endif

        <form method="POST" action="{{ route('login.verify') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $mobile }}">

            <label for="code">Enter 6-digit OTP</label>
            <input type="text" id="code" name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required autofocus>

            <div style="margin-top:18px;">
                <button class="btn" type="submit">VERIFY &amp; LOGIN</button>
            </div>
        </form>

        <div style="margin-top:10px;">
            <a class="btn btn-outline" href="{{ route('login') }}">&larr; CHANGE NUMBER / RESEND</a>
        </div>
    @else
        <p style="font-size:13.5px; color:#444; margin:6px 0 14px;">Retailer? Order at the <a href="{{ route('retailer.home') }}" style="color:#111; font-weight:600;">retailer portal &rarr;</a></p>

    <form method="POST" action="{{ route('login.otp') }}">
            @csrf

            <label for="mobile">Mobile number</label>
            <input type="tel" id="mobile" name="mobile" inputmode="numeric" maxlength="10" value="{{ old('mobile') }}" placeholder="10-digit mobile" required autofocus>

            <div style="margin-top:18px;">
                <button class="btn" type="submit">SEND OTP</button>
            </div>
        </form>
    @endif
@endsection
