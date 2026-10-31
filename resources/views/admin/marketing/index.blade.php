<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marketing performance | HM CARS Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px 48px; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        main { width: min(1200px, 100%); margin: 0 auto; }
        header, .panel, .stat-card { margin-bottom: 18px; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; background: #10233f; color: white; }
        header h1 { margin: 0 0 6px; font-size: 24px; }
        header p { margin: 0; color: #d8e2f0; }
        h2 { margin: 0 0 14px; font-size: 19px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; }
        .button.secondary { background: white; color: #10233f; }
        .summary, .platform-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
        .stat-card { min-width: 0; margin: 0; }
        .label { display: block; color: #64748b; font-size: 13px; font-weight: 700; }
        .value { display: block; margin: 7px 0 4px; color: #10233f; font-size: 29px; font-weight: 800; }
        .note { color: #64748b; font-size: 12px; }
        .platform-card { padding: 17px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; }
        .platform-card h3 { margin: 0 0 14px; font-size: 16px; text-transform: capitalize; }
        .metric-row { display: flex; justify-content: space-between; gap: 8px; padding: 8px 0; border-top: 1px solid #eef2f7; font-size: 13px; }
        .metric-row strong { color: #10233f; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 750px; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #334155; font-size: 13px; }
        td { font-size: 14px; }
        td a { color: #1d4ed8; font-weight: 700; text-decoration: none; }
        td a:hover { text-decoration: underline; }
        .subtext { display: block; margin-top: 4px; color: #64748b; font-size: 12px; }
        .empty { padding: 22px; color: #64748b; text-align: center; }
        .pagination { padding-top: 14px; }
        .pagination svg { width: 18px; height: 18px; }
        @media (max-width: 760px) { .summary, .platform-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 440px) { body { padding: 12px 10px 30px; } header, .panel { padding: 16px; } .summary, .platform-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>Marketing performance</h1>
            <p>Campaign reach and inquiries from social promotion links.</p>
        </div>
        <a class="button secondary" href="{{ route('admin.dashboard') }}">Back to dashboard</a>
    </header>

    <section class="summary" aria-label="Overall marketing totals">
        <article class="stat-card">
            <span class="label">Promotions</span>
            <strong class="value">{{ number_format($totals->promotions) }}</strong>
            <span class="note">Recorded social campaigns</span>
        </article>
        <article class="stat-card">
            <span class="label">Tracked visits</span>
            <strong class="value">{{ number_format($totals->visits) }}</strong>
            <span class="note">Unique visits per campaign and browser session</span>
        </article>
        <article class="stat-card">
            <span class="label">Inquiries</span>
            <strong class="value">{{ number_format($totals->inquiries) }}</strong>
            <span class="note">Inquiries attributed to social promotions</span>
        </article>
        <article class="stat-card">
            <span class="label">Ready to promote</span>
            <strong class="value">{{ number_format($eligibleVehicles) }}</strong>
            <span class="note">Published listings with marketing permission</span>
        </article>
    </section>

    <section class="panel">
        <h2>Performance by platform</h2>
        <div class="platform-grid">
            @foreach ($platformStats as $stats)
                <article class="platform-card">
                    <h3>{{ $stats->platform }}</h3>
                    <div class="metric-row"><span>Promotions</span><strong>{{ number_format($stats->promotions_count) }}</strong></div>
                    <div class="metric-row"><span>Tracked visits</span><strong>{{ number_format($stats->visits_count) }}</strong></div>
                    <div class="metric-row"><span>Inquiries</span><strong>{{ number_format($stats->inquiries_count) }}</strong></div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <h2>Campaign history</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Platform</th>
                        <th>Promoted</th>
                        <th>Admin</th>
                        <th>Visits</th>
                        <th>Inquiries</th>
                        <th>Listing status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPromotions as $promotion)
                        <tr>
                            <td>
                                @if ($promotion->vehicle && $promotion->vehicle->status === 'published' && $promotion->vehicle->marketing_authorized_at)
                                    <a href="{{ route('admin.vehicles.promote', $promotion->vehicle) }}">
                                        {{ $promotion->vehicle->year }} {{ $promotion->vehicle->make }} {{ $promotion->vehicle->model }}
                                    </a>
                                @elseif ($promotion->vehicle)
                                    {{ $promotion->vehicle->year }} {{ $promotion->vehicle->make }} {{ $promotion->vehicle->model }}
                                @else
                                    Vehicle no longer available
                                @endif
                                @if ($promotion->vehicle)
                                    <span class="subtext">HMC-{{ str_pad((string) $promotion->vehicle->id, 4, '0', STR_PAD_LEFT) }}</span>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::headline($promotion->platform) }}</td>
                            <td>{{ $promotion->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $promotion->promoter?->name ?? 'Former admin' }}</td>
                            <td>{{ number_format($promotion->visits_count) }}</td>
                            <td>{{ number_format($promotion->inquiries_count) }}</td>
                            <td>{{ $promotion->vehicle ? \Illuminate\Support\Str::headline($promotion->vehicle->status) : 'Unavailable' }}</td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="7">No social promotions have been recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $recentPromotions->links() }}</div>
    </section>
</main>
</body>
</html>
