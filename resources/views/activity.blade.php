@extends('layouts.app')

@section('title', 'Activity')

@section('content')
    <style>
        main.container { max-width: 800px; }
        .statgrid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .statgrid .card { margin: 0; }
        .statval { font-size: 20px; font-weight: 700; }
        .statsub { font-size: 12.5px; font-weight: 600; color: #1a1a1a; margin-top: 2px; }
        .iplink { color: #1a1a1a; font-weight: 600; text-decoration: underline dotted; white-space: nowrap; }
        .locline { font-size: 12px; font-weight: 600; color: #444; }
    </style>

    <h2>Activity</h2>
    <hr class="rule">

    @php
        $q = fn ($p, $a) => route('activity.index') . '?' . http_build_query(['period' => $p, 'actor' => $a]);
    @endphp

    <div class="fbox">
        <div class="chip-grid-4">
            <a class="btn {{ $period === 'today' ? '' : 'btn-outline' }}" href="{{ $q('today', $actor) }}">TODAY</a>
            <a class="btn {{ $period === '7d' ? '' : 'btn-outline' }}" href="{{ $q('7d', $actor) }}">7 DAYS</a>
            <a class="btn {{ $period === '30d' ? '' : 'btn-outline' }}" href="{{ $q('30d', $actor) }}">30 DAYS</a>
            <a class="btn {{ $period === 'all' ? '' : 'btn-outline' }}" href="{{ $q('all', $actor) }}">ALL</a>
        </div>
        <div class="chip-grid-4">
            <a class="btn {{ $actor === 'all' ? '' : 'btn-outline' }}" href="{{ $q($period, 'all') }}">EVERYONE</a>
            <a class="btn {{ $actor === 'owner' ? '' : 'btn-outline' }}" href="{{ $q($period, 'owner') }}">OWNER</a>
            <a class="btn {{ $actor === 'partner' ? '' : 'btn-outline' }}" href="{{ $q($period, 'partner') }}">RETAILERS</a>
            <a class="btn {{ $actor === 'guest' ? '' : 'btn-outline' }}" href="{{ $q($period, 'guest') }}">VISITORS</a>
        </div>
    </div>

    <div class="statgrid">
        <div class="card">
            <div class="stitle">TOTAL HITS</div>
            <div class="statval">{{ number_format($stats->hits) }}</div>
            <div class="statsub">requests in period</div>
        </div>
        <div class="card">
            <div class="stitle">UNIQUE VISITORS</div>
            <div class="statval">{{ number_format($stats->ips) }}</div>
            <div class="statsub">by IP address</div>
        </div>
        <div class="card">
            <div class="stitle">OWNER / RETAILER</div>
            <div class="statval">{{ number_format($stats->owner_hits ?? 0) }} / {{ number_format($stats->partner_hits ?? 0) }}</div>
            <div class="statsub">logged-in activity</div>
        </div>
        <div class="card">
            <div class="stitle">ANONYMOUS</div>
            <div class="statval">{{ number_format($stats->guest_hits ?? 0) }}</div>
            <div class="statsub">not logged in</div>
        </div>
    </div>

    <h3 class="stitle" style="margin:16px 0 6px;">DISTINCT VISITORS</h3>
    @forelse ($visitors as $v)
        <div class="dcard">
            <div class="dcard-row">
                <span style="font-weight:700;">
                    {{ $v->loc ?? 'Unknown location' }}
                </span>
                <b>{{ $v->hits }} {{ $v->hits == 1 ? 'hit' : 'hits' }}</b>
            </div>
            <div class="dcard-part dcard-row locline">
                <span>
                    @if ($v->actor_names) {{ $v->actor_names }} @else Anonymous @endif
                    ({{ str_replace(['owner', 'partner', 'guest'], ['owner', 'retailer', 'visitor'], $v->actor_types) }})
                </span>
                <a class="iplink" href="https://ipinfo.io/{{ $v->ip }}" target="_blank" rel="noopener">{{ $v->ip }}</a>
            </div>
            <div class="locline" style="margin-top:4px;">
                First {{ \Carbon\Carbon::parse($v->first_seen)->format('d/m H:i') }}
                &middot; Last {{ \Carbon\Carbon::parse($v->last_seen)->format('d/m H:i') }}
            </div>
        </div>
    @empty
        <p class="muted">No visitors in this period.</p>
    @endforelse

    <h3 class="stitle" style="margin:16px 0 6px;">WHAT WAS DONE</h3>
    <div class="rtblwrap" style="overflow-x:auto;">
        <table class="rtable">
            <thead><tr><th>Action</th><th class="num" style="width:90px;">Times</th></tr></thead>
            <tbody>
                @forelse ($summary as $label => $count)
                    <tr>
                        <td style="font-weight:600;">{{ $label }}</td>
                        <td class="num" style="font-weight:700;">{{ $count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" style="font-weight:600; padding:10px 6px;">No activity in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="rcards">
        @foreach ($summary as $label => $count)
            <div class="dcard dcard-row">
                <span style="font-weight:600;">{{ $label }}</span>
                <b>{{ $count }}</b>
            </div>
        @endforeach
    </div>

    <h3 class="stitle" style="margin:18px 0 6px;">RECENT TRAIL (LAST 100)</h3>
    @foreach ($trail as $t)
        <div class="dcard">
            <div class="dcard-row">
                <span style="font-weight:700;">{{ $t->label }}</span>
                <span class="muted" style="white-space:nowrap;">{{ \Carbon\Carbon::parse($t->created_at)->format('d/m H:i') }}</span>
            </div>
            <div class="dcard-part dcard-row locline">
                <span>
                    @if ($t->actor_type === 'owner') Owner{{ $t->actor_name ? ' · ' . $t->actor_name : '' }}
                    @elseif ($t->actor_type === 'partner') Retailer · {{ $t->actor_name }}
                    @else Visitor @endif
                    @if ($t->loc) &middot; {{ $t->loc }} @endif
                </span>
                <a class="iplink" href="https://ipinfo.io/{{ $t->ip }}" target="_blank" rel="noopener">{{ $t->ip }}</a>
            </div>
        </div>
    @endforeach

    <p class="callout" style="margin-top:14px;">
        Locations are approximate (city-level; mobile networks can show the telecom hub instead of the person's town). Local testing shows as "Local network".
    </p>
@endsection
