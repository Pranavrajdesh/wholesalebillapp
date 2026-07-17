<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->subDays(6);
        $monthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $monthStart->copy()->subMonth();
        $lastMonthEnd = $monthStart->copy()->subDay();

        // ---- Sales ----
        $salesToday = (float) DB::table('invoices')->whereDate('invoice_date', $today)->sum('total');
        $billsToday = (int) DB::table('invoices')->whereDate('invoice_date', $today)->count();
        $salesWeek = (float) DB::table('invoices')->whereBetween('invoice_date', [$weekStart, $today])->sum('total');
        $salesMonth = (float) DB::table('invoices')->whereBetween('invoice_date', [$monthStart, $today])->sum('total');
        $salesLastMonth = (float) DB::table('invoices')->whereBetween('invoice_date', [$lastMonthStart, $lastMonthEnd])->sum('total');

        // ---- Collections ----
        $paymentsToday = (float) DB::table('payments')->whereDate('payment_date', $today)->sum('amount');
        $paymentsMonth = (float) DB::table('payments')->whereBetween('payment_date', [$monthStart, $today])->sum('amount');

        // ---- Receivable: per-partner net (invoices - payments - credit notes), summed by sign ----
        $inv = DB::table('invoices')->select('partner_id', DB::raw('SUM(total) AS t'))->groupBy('partner_id')->pluck('t', 'partner_id');
        $pay = DB::table('payments')->select('partner_id', DB::raw('SUM(amount) AS t'))->groupBy('partner_id')->pluck('t', 'partner_id');
        $cn = DB::table('credit_notes')->select('partner_id', DB::raw('SUM(total) AS t'))->groupBy('partner_id')->pluck('t', 'partner_id');

        $receivable = 0.0;
        $advanceHeld = 0.0;
        $duePartners = 0;
        foreach (array_unique(array_merge($inv->keys()->all(), $pay->keys()->all(), $cn->keys()->all())) as $pid) {
            $bal = (float) ($inv[$pid] ?? 0) - (float) ($pay[$pid] ?? 0) - (float) ($cn[$pid] ?? 0);
            if ($bal > 0.009) {
                $receivable += $bal;
                $duePartners++;
            } elseif ($bal < -0.009) {
                $advanceHeld += abs($bal);
            }
        }

        // ---- Payable: per-supplier net (bills - payments) ----
        $sbill = DB::table('supplier_bills')->select('supplier_id', DB::raw('SUM(amount) AS t'))->groupBy('supplier_id')->pluck('t', 'supplier_id');
        $spay = DB::table('supplier_payments')->select('supplier_id', DB::raw('SUM(amount) AS t'))->groupBy('supplier_id')->pluck('t', 'supplier_id');

        $payable = 0.0;
        $dueSuppliers = 0;
        foreach (array_unique(array_merge($sbill->keys()->all(), $spay->keys()->all())) as $sid) {
            $bal = (float) ($sbill[$sid] ?? 0) - (float) ($spay[$sid] ?? 0);
            if ($bal > 0.009) {
                $payable += $bal;
                $dueSuppliers++;
            }
        }

        // ---- Alerts ----
        $pendingOrders = (int) Order::where('status', 'pending')->count();
        $lowStock = (int) Product::where('is_active', true)->whereBetween('stock_qty', [0, 10])->count();
        $negativeStock = (int) Product::where('is_active', true)->where('stock_qty', '<', 0)->count();

        // ---- 12-month sales series (oldest first) ----
        $seriesStart = $monthStart->copy()->subMonths(11);
        $rows = DB::table('invoices')
            ->select(DB::raw("DATE_FORMAT(invoice_date, '%Y-%m') AS ym"), DB::raw('SUM(total) AS t'))
            ->where('invoice_date', '>=', $seriesStart->toDateString())
            ->groupBy('ym')
            ->pluck('t', 'ym');

        $series = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $seriesStart->copy()->addMonths($i);
            $series[] = [
                'label' => $m->format('M'),
                'ym' => $m->format('Y-m'),
                'value' => (float) ($rows[$m->format('Y-m')] ?? 0),
            ];
        }

        return view('dashboard', [
            'salesToday' => $salesToday,
            'billsToday' => $billsToday,
            'salesWeek' => $salesWeek,
            'salesMonth' => $salesMonth,
            'salesLastMonth' => $salesLastMonth,
            'paymentsToday' => $paymentsToday,
            'paymentsMonth' => $paymentsMonth,
            'receivable' => $receivable,
            'advanceHeld' => $advanceHeld,
            'duePartners' => $duePartners,
            'payable' => $payable,
            'dueSuppliers' => $dueSuppliers,
            'pendingOrders' => $pendingOrders,
            'lowStock' => $lowStock,
            'negativeStock' => $negativeStock,
            'series' => $series,
        ]);
    }
}
