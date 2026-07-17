# wholesaleBillApp

A complete billing and order-management system for Indian FMCG wholesalers — billing, inventory, partner (retailer) accounts, supplier purchases, a retailer ordering portal, dashboard, and a full reports suite. Built mobile-first: the owner runs the business from a phone; retailers order from an installable, firm-branded web app.

## Features

**Billing** — partner-wise carts with rate slabs (qty-based rates, scheme %, free goods), manual rate helpers (factor / flat % off MRP), tax-inclusive and exclusive pricing, held bills, stock guard (configurable negative-stock policy), discount + round-off, and a projected-profit block.

**Invoices** — A4 / A5 / 3-inch thermal print layouts, PDF download, WhatsApp share, signed public links, HSN-wise tax summary (CGST/SGST back-out from inclusive amounts), UPI scan-to-pay QR with the bill amount.

**Partners** — retailer accounts with mobile-OTP identity, per-partner rate visibility gates, ledgers (invoices / payments / credit notes) with FY filters, running balances, signed shareable ledger links, and ledger PDFs with bank details.

**Suppliers & Purchases** — supplier accounts, bill and payment recording with ledgers (mirroring partner ledgers), and stock-inward entries (decoupled from bills by design: bills drive the supplier ledger, inward drives stock).

**Retailer portal** (`/retailer`) — token-authenticated (Sanctum) ordering app: catalogue with the partner's own slab rates (or rates hidden per gate), cart, order placement with notes, order history, cancellation, and invoice access. Installable as a PWA named after the wholesaler's firm.

**Orders inbox** — review incoming retailer orders with slab-suggested pricing, load into the billing cart (with held-bill safety), invoice, and the status trail (pending → invoiced / cancelled).

**Dashboard** — today/week/month sales, collections, receivable/payable money position, pending-order and stock alerts, 12-month sales chart.

**Reports** (7) — Sales Register, Sales Summary (by month/partner/brand/product), Collections Register, Outstanding & Aging (FIFO settlement into 30/60/90+ buckets), Stock Report, Purchase Register, GST/HSN Summary. Every report: on-screen (desktop tables + mobile cards), CSV, and a formal PDF; FY presets (Indian Apr–Mar), custom ranges, clear-filter.

**PWA** — installable owner app and firm-branded retailer app, service worker with offline fallback (no offline writes by design — the server is the source of truth).

## Stack

- **Laravel 12** (PHP 8.3), MySQL 8
- Blade views, vanilla JS (no front-end framework), plain CSS with a utility layer
- Vite for assets; laravel-dompdf for PDFs; Sanctum for the retailer API
- Local dev: Laravel Herd on Windows (HTTPS via `herd secure`)

## Local setup

```bash
git clone <repo> wholesaleBillApp && cd wholesaleBillApp
composer install
npm install
cp .env.example .env          # set DB_DATABASE / DB_USERNAME / DB_PASSWORD
php artisan key:generate
php artisan migrate
npm run dev                   # keep running; use `npm run build` for production assets
```

Serve with Herd (`herd link wholesalebillapp && herd secure wholesalebillapp`) or `php artisan serve`. HTTPS is required for the PWA/service worker (localhost is exempt).

**Login**: OTP-based; in dev mode the OTP is displayed on screen (marked clearly). The owner user is created via seeding/tinker; retailer partners log in at `/retailer` when active + portal-enabled.

## Architecture map

```
app/Http/Controllers/        one controller per domain:
  Billing, Invoice, Product, Partner, Payment, CreditNote,
  Supplier, SupplierLedger, Inward, Order, RateSlab,
  Dashboard, Report (7 reports, shared period/csv/pdf helpers),
  Login (session OTP), Setting
app/Http/Controllers/Api/    retailer channel (token auth):
  RetailerAuth, RetailerCatalogue, RetailerOrder
app/Services/OtpService.php  OTP issue/verify (SMS gateway hook pending)
app/Support/helpers.php      global inr() — Indian number formatting
resources/js/
  billing.js   shared money/slab/cart math + notify/search/composer factories
  ui.js        menu + shared UI glue
  invoice.js   invoice page behavior
resources/views/
  layouts/app.blade.php      owner chrome (header, nav, MORE menu)
  reports/_topbar, _actions  shared report chrome; reports/pdf is the
                             one generic PDF template all reports feed
  retailer/portal.blade.php  standalone portal SPA-lite (token in localStorage)
routes/web.php               owner app + signed public links + manifests
routes/api.php               /api/retailer/* (throttled OTP, Sanctum-guarded data)
```

**Key design decisions**
- Single-tenant: one wholesaler per deployment (multi-tenant is a separate product track).
- Bills ≠ inward: supplier money and stock movement are recorded independently.
- No offline writes: the PWA caches shell + offline page only.
- Signed URLs for anything shared outward (invoices, ledgers) — tamper-proof, no login needed.
- Aging report settles payments/credit notes against oldest invoices first (FIFO).

## UI conventions (the "design language")

Monochrome ink (`#1a1a1a`) on light grey, white cards; semantic color only for meaning: red = due/negative, amber = advance/warning, green = settled/ok. Utility classes in `resources/css/app.css`: `.dcard/.dcard-row/.dcard-part` (bordered cards with dashed partitions), `table.rtable` (report/statement tables), `.callout{,-red,-amber,-green}`, `.chip-grid-4` (button grids, 2×2 on mobile), `.stitle`, `.moneyline`. Document-style pages (ledgers, invoices, reports) run 800px; app pages 650px. Desktop shows tables; mobile shows card lists. New pages: utility classes + one `<style>` + one `<script>` block, no inline styles. (Older views are being migrated to this standard — see roadmap.)

## API surface (retailer channel)

```
POST /api/retailer/request-otp     mobile → OTP (gated: registered + active + portal access)
POST /api/retailer/verify-otp      mobile + code → Sanctum token + profile
GET  /api/retailer/me              profile
GET  /api/retailer/filters         brands/categories
GET  /api/retailer/products        catalogue with per-partner rate gates + slabs
GET/POST /api/retailer/orders...   place, list, view, cancel
```

This API is the integration point for the planned standalone retailer billing app (see roadmap).

## Roadmap

- SMS gateway for OTP (replace on-screen dev OTP; MSG91/Fast2SMS/Twilio behind OtpService)
- CSS hygiene: migrate remaining older views onto the utility classes; extract a shared `_filterbox` partial; unify the partner/supplier ledger views
- Deployment: single VPS, per-client codebase checkout + `.env` + database
- **Phase 2 — retailer billing app**: a separate product letting retailers bill their own walk-in customers, with the catalogue auto-synced from their wholesaler via the existing retailer API
