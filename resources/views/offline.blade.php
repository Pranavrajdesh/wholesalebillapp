<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1a1a1a">
    <title>Offline</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #ececec; color: #1a1a1a; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px; }
        .box { background: #fff; border: 1px solid #1a1a1a; border-radius: 6px; padding: 28px 24px; max-width: 340px; width: 100%; text-align: center; }
        .mark { width: 64px; height: 64px; background: #1a1a1a; color: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 34px; font-weight: 700; margin: 0 auto 16px; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        p { font-size: 13.5px; font-weight: 600; color: #1a1a1a; margin: 0 0 18px; line-height: 1.5; }
        button { width: 100%; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 4px; font-size: 13px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <div class="mark">W</div>
        <h1>You're offline</h1>
        <p>wholesaleBillApp needs an internet connection &mdash; billing and stock always use live data so nothing goes out of sync.</p>
        <button onclick="location.reload()">TRY AGAIN</button>
    </div>
</body>
</html>
