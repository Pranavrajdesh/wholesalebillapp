<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $period = in_array($request->query('period'), ['today', '7d', '30d', 'all'], true)
            ? $request->query('period') : 'today';
        $actor = in_array($request->query('actor'), ['all', 'owner', 'partner', 'guest'], true)
            ? $request->query('actor') : 'all';

        $from = match ($period) {
            'today' => Carbon::today(),
            '7d' => Carbon::today()->subDays(6),
            '30d' => Carbon::today()->subDays(29),
            default => null,
        };

        $base = DB::table('activity_log');
        if ($from) $base->where('created_at', '>=', $from);
        if ($actor !== 'all') $base->where('actor_type', $actor);

        // ---- Stat cards (respect period, not actor filter) ----
        $stats = DB::table('activity_log');
        if ($from) $stats->where('created_at', '>=', $from);
        $statRows = $stats->selectRaw("COUNT(*) AS hits, COUNT(DISTINCT ip) AS ips,
                SUM(actor_type = 'owner') AS owner_hits,
                SUM(actor_type = 'partner') AS partner_hits,
                SUM(actor_type = 'guest') AS guest_hits")->first();

        // ---- Distinct visitors (grouped by IP) ----
        $visitors = (clone $base)->selectRaw("ip, COUNT(*) AS hits,
                MIN(created_at) AS first_seen, MAX(created_at) AS last_seen,
                GROUP_CONCAT(DISTINCT actor_type ORDER BY actor_type SEPARATOR '/') AS actor_types,
                GROUP_CONCAT(DISTINCT actor_name ORDER BY actor_name SEPARATOR ', ') AS actor_names")
            ->groupBy('ip')->orderByDesc('hits')->limit(50)->get();

        // ---- Action summary (grouped by friendly label) ----
        $rows = (clone $base)->orderByDesc('created_at')->limit(2000)->get();
        $summary = [];
        foreach ($rows as $r) {
            $label = $this->label($r);
            $summary[$label] = ($summary[$label] ?? 0) + 1;
        }
        arsort($summary);

        // ---- Recent trail ----
        $trail = (clone $base)->orderByDesc('id')->limit(100)->get()->map(function ($r) {
            $r->label = $this->label($r);
            return $r;
        });

        // ---- Locations: cached, look up at most 10 new IPs per page load ----
        $ips = $visitors->pluck('ip')->merge($trail->pluck('ip'))->filter()->unique()->values();
        $locs = $this->locations($ips);

        $visitors->each(fn ($v) => $v->loc = $locs[$v->ip] ?? null);
        $trail->each(fn ($t) => $t->loc = $locs[$t->ip] ?? null);

        return view('activity', [
            'period' => $period,
            'actor' => $actor,
            'stats' => $statRows,
            'visitors' => $visitors,
            'summary' => $summary,
            'trail' => $trail,
        ]);
    }

    /** @return array<string,string|null> ip => "City, Region · ISP" */
    private function locations($ips): array
    {
        $known = DB::table('ip_locations')->whereIn('ip', $ips)->get()->keyBy('ip');
        $lookups = 0;

        foreach ($ips as $ip) {
            if (isset($known[$ip]) || $lookups >= 10) continue;

            if ($ip === '127.0.0.1' || $ip === '::1'
                || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
                DB::table('ip_locations')->insertOrIgnore([
                    'ip' => $ip, 'city' => 'Local network', 'looked_up_at' => now(),
                ]);
                $known[$ip] = (object) ['city' => 'Local network', 'region' => null, 'org' => null];
                continue;
            }

            $lookups++;
            try {
                $res = Http::timeout(2)->get('http://ip-api.com/json/' . $ip . '?fields=status,city,regionName,countryCode,isp');
                $d = $res->json();
                if (($d['status'] ?? '') === 'success') {
                    $row = [
                        'ip' => $ip,
                        'city' => $d['city'] ?? null,
                        'region' => $d['regionName'] ?? null,
                        'country' => $d['countryCode'] ?? null,
                        'org' => $d['isp'] ?? null,
                        'looked_up_at' => now(),
                    ];
                    DB::table('ip_locations')->insertOrIgnore($row);
                    $known[$ip] = (object) $row;
                }
            } catch (\Throwable $e) {
                // Lookup failure: stays unknown; retried on a future load
            }
        }

        $out = [];
        foreach ($known as $ip => $k) {
            $parts = array_filter([$k->city ?? null, $k->region ?? null]);
            $loc = implode(', ', $parts);
            if (!empty($k->org)) $loc .= ($loc ? ' · ' : '') . $k->org;
            $out[$ip] = $loc ?: null;
        }
        return $out;
    }

    private function label(object $r): string
    {
        $name = (string) ($r->route_name ?? '');
        $path = strtok((string) $r->path, '?');
        $m = $r->method;

        $map = [
            'login' => 'Opened login page',
            'login.otp' => 'Requested owner OTP',
            'login.verify' => 'Verified owner OTP',
            'logout' => 'Logged out',
            'dashboard' => 'Viewed dashboard',
            'products.index' => 'Viewed products',
            'billing.select' => 'Opened new bill (partner select)',
            'billing.catalogue' => 'Browsed billing catalogue',
            'billing.cart' => 'Viewed cart',
            'billing.checkout' => 'Opened checkout',
            'invoices.index' => 'Viewed invoice list',
            'invoices.show' => 'Viewed an invoice',
            'partners.index' => 'Viewed partners',
            'ledger.show' => 'Viewed a partner ledger',
            'orders.index' => 'Viewed partner orders',
            'suppliers.index' => 'Viewed suppliers',
            'inward.index' => 'Viewed stock inward',
            'settings.edit' => 'Opened settings',
            'reports.index' => 'Opened reports',
            'reports.sales_register' => 'Viewed sales register',
            'reports.sales_summary' => 'Viewed sales summary',
            'reports.collections' => 'Viewed collections report',
            'reports.outstanding' => 'Viewed outstanding & aging',
            'reports.stock' => 'Viewed stock report',
            'reports.purchases' => 'Viewed purchase register',
            'reports.gst' => 'Viewed GST summary',
        ];
        if ($name && isset($map[$name])) {
            $label = $map[$name];
            if (str_starts_with($name, 'reports.')) {
                if (str_contains((string) $r->path, 'format=csv')) $label .= ' — CSV download';
                if (str_contains((string) $r->path, 'format=pdf')) $label .= ' — PDF download';
            }
            return $label;
        }

        if ($m === 'POST') {
            return match (true) {
                str_contains($path, '/billing/save') => 'SAVED AN INVOICE',
                str_contains($path, '/payments') => 'RECORDED A PAYMENT',
                str_contains($path, '/partners') => 'Saved a partner',
                str_contains($path, '/products') => 'Saved a product',
                str_contains($path, '/suppliers') => 'Saved a supplier',
                str_contains($path, '/inward') => 'Saved stock inward',
                str_contains($path, '/api/retailer/request-otp') => 'Portal: requested OTP',
                str_contains($path, '/api/retailer/verify-otp') => 'Portal: verified OTP (login)',
                str_contains($path, '/api/retailer/orders') && str_contains($path, 'cancel') => 'Portal: cancelled an order',
                str_contains($path, '/api/retailer/orders') => 'PORTAL: PLACED AN ORDER',
                default => 'Action: ' . $m . ' ' . $path,
            };
        }

        return match (true) {
            str_contains($path, '/api/retailer/products') => 'Portal: browsed catalogue',
            str_contains($path, '/api/retailer/orders') => 'Portal: viewed orders',
            str_contains($path, '/api/retailer/filters') => 'Portal: loaded filters',
            str_contains($path, '/api/retailer/me') => 'Portal: session resumed',
            $path === '/retailer' => 'Opened retailer portal',
            str_starts_with($path, '/pub/') || str_contains($path, 'signature=') => 'Opened a shared link',
            str_contains($path, '/pdf') => 'Downloaded a PDF',
            $path === '/' => 'Opened home',
            default => 'Viewed ' . $path,
        };
    }
}
