<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vehicle Submissions | HM CARS Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        header { padding: 18px 24px; background: #10233f; color: white; }
        header h1 { margin: 0; font-size: 21px; }
        main { width: min(1240px, calc(100% - 32px)); margin: 26px auto; }
        .topbar, .top-actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .topbar { margin-bottom: 18px; }
        h2 { margin: 0 0 5px; }
        p { color: #64748b; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; cursor: pointer; }
        .button.secondary { background: white; color: #10233f; }
        .panel { overflow: hidden; border: 1px solid #e2e8f0; border-radius: 8px; background: white; }
        .filters { display: flex; align-items: end; gap: 10px; flex-wrap: wrap; padding: 14px; border-bottom: 1px solid #e2e8f0; }
        label { display: grid; gap: 5px; color: #475569; font-size: 13px; font-weight: 700; }
        select { min-height: 40px; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 5px; background: white; color: #1b2638; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; min-width: 760px; border-collapse: collapse; }
        th, td { padding: 13px 14px; border-bottom: 1px solid #e5eaf1; text-align: left; vertical-align: top; }
        th { background: #eaf0f7; color: #10233f; font-size: 13px; }
        td { font-size: 14px; }
        td small { color: #64748b; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 4px; background: #eaf0f7; color: #334155; font-size: 12px; font-weight: 700; }
        .pagination { padding: 14px; }
        .empty { padding: 28px; color: #64748b; text-align: center; }
        @media (max-width: 560px) { main { width: calc(100% - 20px); margin-top: 16px; } header { padding-inline: 16px; } }
    </style>
</head>
<body>
<header><h1>HM CARS · Vehicle submissions</h1></header>
<main>
    <div class="topbar">
        <div>
            <h2>Seller submissions</h2>
            <p>Review private vehicle submissions before publishing them.</p>
        </div>
        <div class="top-actions">
            <a class="button secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="button" type="submit">Log out</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif

    <section class="panel">
        <form class="filters" method="GET" action="{{ route('admin.submissions.index') }}">
            <label for="submission-status">Status
                <select id="submission-status" name="status">
                    <option value="">All submissions</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ \Illuminate\Support\Str::headline($status) }}
                        </option>
                    @endforeach
                </select>
            </label>
            <button class="button" type="submit">Filter</button>
            @if (request()->filled('status'))
                <a href="{{ route('admin.submissions.index') }}">Clear</a>
            @endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Submission</th>
                        <th>Vehicle</th>
                        <th>Seller</th>
                        <th>Asking price</th>
                        <th>Photos</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>#{{ $submission->id }}</td>
                            <td>{{ $submission->year }} {{ $submission->make }} {{ $submission->model }}</td>
                            <td>
                                {{ $submission->seller_name }}<br>
                                <small>
                                    {{ $submission->seller_phone }}<br>
                                    {{ $submission->seller_email ?: 'No email provided' }}
                                </small>
                            </td>
                            <td>KSh {{ number_format((float) $submission->price) }}</td>
                            <td>{{ $submission->images_count }}</td>
                            <td><span class="status">{{ \Illuminate\Support\Str::headline($submission->status) }}</span></td>
                            <td>{{ $submission->created_at?->format('d M Y, H:i') }}</td>
                            <td><a href="{{ route('admin.submissions.show', $submission) }}">Review</a></td>
                        </tr>
                    @empty
                        <tr><td class="empty" colspan="8">No submissions match this status.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $submissions->links() }}</div>
    </section>
</main>
</body>
</html>