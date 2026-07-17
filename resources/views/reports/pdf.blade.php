<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #111; }
        .wrap { border: 2px solid #111; padding: 14px 16px; }
        table { width: 100%; border-collapse: collapse; }
        .headtbl td { border-bottom: 2px solid #111; padding-bottom: 8px; vertical-align: top; }
        .firm { font-size: 16px; font-weight: bold; }
        .firmmeta { font-size: 9.5px; font-weight: bold; color: #1a1a1a; line-height: 1.6; margin-top: 2px; }
        .doctitle { font-size: 11px; font-weight: bold; letter-spacing: 1px; color: #1a1a1a; text-align: right; }
        .docperiod { font-size: 9.5px; font-weight: bold; text-align: right; margin-top: 2px; }
        table.rep { margin-top: 10px; }
        table.rep th { background: #f2f2f2; border-top: 1.5px solid #111; border-bottom: 1.5px solid #111; border-left: 1px solid #999; border-right: 1px solid #999; padding: 4px 5px; font-size: 8.5px; }
        table.rep td { border: 1px solid #999; padding: 3px 5px; font-size: 9px; }
        table.rep .l { text-align: left; }
        table.rep .r { text-align: right; white-space: nowrap; }
        table.rep tr.tot td { font-weight: bold; background: #f7f7f7; border-top: 1.5px solid #111; }
        .note { margin-top: 10px; border: 1px solid #999; border-left: 4px solid #111; background: #f7f7f7; padding: 8px 10px; font-size: 9px; font-weight: bold; }
        .foot { margin-top: 10px; font-size: 8.5px; font-weight: bold; color: #1a1a1a; }
    </style>
</head>
<body>
<div class="wrap">

    <table class="headtbl">
        <tr>
            <td>
                <div class="firm">{{ $s['firm_name'] ?? '' }}</div>
                <div class="firmmeta">
                    @if (!empty($s['firm_gst'])) GSTIN: {{ $s['firm_gst'] }}<br>@endif
                    Mobile: {{ $s['firm_mobile'] ?? '' }}
                </div>
            </td>
            <td style="width:220px;">
                <div class="doctitle">{{ strtoupper($title) }}</div>
                <div class="docperiod">{{ $period }}</div>
            </td>
        </tr>
    </table>

    <table class="rep">
        <thead>
            <tr>
                @foreach ($columns as [$label, $align])
                    <th class="{{ $align }}">{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $i => $cell)
                        <td class="{{ $columns[$i][1] }}">{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($columns) }}" style="font-weight:bold; padding:8px 5px;">No data in this period.</td></tr>
            @endforelse
            @if ($totals)
                <tr class="tot">
                    @foreach ($totals as $i => $cell)
                        <td class="{{ $columns[$i][1] ?? 'l' }}">{{ $cell }}</td>
                    @endforeach
                </tr>
            @endif
        </tbody>
    </table>

    @if ($note)
        <div class="note">{{ $note }}</div>
    @endif

    <div class="foot">
        Generated on {{ now()->format('d M Y, h:i A') }}. This is a computer-generated report.
    </div>

</div>
</body>
</html>
