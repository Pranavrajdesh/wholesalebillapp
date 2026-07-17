<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a1a">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <title>@yield('title', 'wholesaleBillApp')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="app">
        <div class="inner">
            @auth
                <a class="storename" href="{{ route('products.index') }}">{{ auth()->user()->name }}</a>
            @elseauth('partner')
                <a class="storename" href="{{ route('retailer.home') }}">{{ auth('partner')->user()->firm_name }}</a>
            @else
                <span class="storename">wholesaleBillApp</span>
            @endauth

            @if (auth()->check() || auth('partner')->check())
                <form method="POST" action="{{ route('logout') }}" class="inlineform">
                    @csrf
                    <button type="submit" class="logoutbtn">Logout</button>
                </form>
            @endif
        </div>
    </header>

    @auth
        <nav class="navbar">
            <div class="inner">
                <a class="navlink" href="{{ route('billing.select') }}">Billing</a>
                <a class="navlink" href="{{ route('products.index') }}">Products</a>
                <a class="navlink" href="{{ route('partners.index') }}">Partners</a>
                <button type="button" id="morebtn" class="navlink navbtn">MORE &#9662;</button>
            </div>
        </nav>

        <div id="menumodal" class="modal" hidden>
            <div class="modal-box">
                <div class="modal-head">
                    <span>Menu</span>
                    <button type="button" id="menuclose" class="xbtn">&times;</button>
                </div>
                <details class="modal-group" open>
                    <summary><span class="mi">&#8377;</span>Billing</summary>
                    <a class="modal-link sub" href="{{ route('billing.select') }}"><span class="mi">&#65291;</span>New Bill</a>
                    <a class="modal-link sub" href="{{ route('billing.select') }}?held=1"><span class="mi">&#10073;&#10073;</span>Held Bills</a>
                    <a class="modal-link sub" href="{{ route('invoices.index') }}"><span class="mi">&#9636;</span>Invoices</a>
                    <a class="modal-link sub" href="{{ route('orders.index') }}"><span class="mi">&#8681;</span>Partner Orders</a>
                </details>
                <details class="modal-group" open>
                    <summary><span class="mi">&#8962;</span>Purchases</summary>
                    <a class="modal-link sub" href="{{ route('suppliers.index') }}"><span class="mi">&#8801;</span>Suppliers</a>
                    <a class="modal-link sub" href="{{ route('inward.index') }}"><span class="mi">&#8682;</span>Stock Inward</a>
                </details>
                <details class="modal-group" open>
                    <summary><span class="mi">&#9636;</span>Products</summary>
                    <a class="modal-link sub" href="{{ route('products.index') }}"><span class="mi">&#8801;</span>Product List</a>
                    <a class="modal-link sub" href="{{ route('products.create') }}"><span class="mi">&#65291;</span>New Product</a>
                    <a class="modal-link sub" href="{{ route('products.import.form') }}"><span class="mi">&#8682;</span>Import CSV</a>
                </details>
                <details class="modal-group" open>
                    <summary><span class="mi">&#9823;</span>Partners</summary>
                    <a class="modal-link sub" href="{{ route('partners.index') }}"><span class="mi">&#8801;</span>Partner List</a>
                    <a class="modal-link sub" href="{{ route('partners.create') }}"><span class="mi">&#65291;</span>New Partner</a>
                </details>
                <details class="modal-group" open>
                    <summary><span class="mi">&#9632;</span>Reports</summary>
                    <a class="modal-link sub" href="{{ route('reports.index') }}"><span class="mi">&#8801;</span>All Reports</a>
                    <a class="modal-link sub" href="{{ route('reports.sales_register') }}"><span class="mi">&#9636;</span>Sales Register</a>
                    <a class="modal-link sub" href="{{ route('reports.sales_summary') }}"><span class="mi">&#9636;</span>Sales Summary</a>
                </details>
                <details class="modal-group" open>
                    <summary><span class="mi">&#9881;</span>Manage App</summary>
                    <a class="modal-link sub" href="{{ route('dashboard') }}"><span class="mi">&#9632;</span>Dashboard</a>
                    <a class="modal-link sub" href="{{ route('settings.edit') }}"><span class="mi">&#9998;</span>Settings</a>
                    <button type="button" id="installbtn" class="modal-link sub" hidden style="width:100%; text-align:left; background:none; border:none; cursor:pointer; font:inherit;"><span class="mi">&#8681;</span>Install App</button>
               </details>
            </div>
        </div>
    @endauth

    <main class="container">
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <script>
        document.addEventListener('click', function (e) {
            const modal = document.getElementById('menumodal');
            if (!modal) return;
            if (e.target.closest('#morebtn')) modal.hidden = false;
            if (e.target.closest('#menuclose') || e.target === modal) modal.hidden = true;
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
        let deferredInstall = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredInstall = e;
            const b = document.getElementById('installbtn');
            if (b) b.hidden = false;
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#installbtn') || !deferredInstall) return;
            deferredInstall.prompt();
            deferredInstall.userChoice.then(() => {
                deferredInstall = null;
                document.getElementById('installbtn').hidden = true;
            });
        });
        window.addEventListener('appinstalled', () => {
            const b = document.getElementById('installbtn');
            if (b) b.hidden = true;
        });

    </script></body>
</html>
