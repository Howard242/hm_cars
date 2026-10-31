
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management | HM CARS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 30px 16px;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #172033;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 8px 30px #17203312;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        h1 { margin: 0 0 8px; }
        .muted { color: #667085; }
        .button {
            display: inline-block;
            padding: 11px 15px;
            border-radius: 7px;
            background: #10233f;
            color: white;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            font-size: 14px;
        }
        .secondary { background: #eaecf0; color: #172033; }
        .danger { background: #b42318; }
        .success-action { background: #027a48; }
        .success {
            background: #ecfdf3;
            color: #027a48;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #eaecf0;
        }
        th { background: #f9fafb; font-size: 13px; }
        .empty { text-align: center; padding: 30px; color: #667085; }
        .pagination { margin-top: 20px; }
        .status { font-weight: 700; }
        .status-active { color: #027a48; }
        .status-suspended { color: #b42318; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .actions form { margin: 0; }
        .actions .button { padding: 8px 10px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <h1>Staff Management</h1>
            <div class="muted">Manage authorized HM CARS staff accounts.</div>
        </div>
        <div>
            <a class="button" href="{{ route('admin.staff.create') }}">+ Add Staff Member</a>
            <a class="button secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </div>
    </div>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Account created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>
                            <span class="status {{ $member->is_active ? 'status-active' : 'status-suspended' }}">
                                {{ $member->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td>{{ $member->created_at?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @unless ($member->is_admin || $member->id === auth()->id())
                                <div class="actions">
                                    @if ($member->is_active)
                                        <form method="POST" action="{{ route('admin.staff.suspend', $member) }}"
                                            onsubmit="return confirm('Suspend this staff account? They will lose access until reactivated.')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="button secondary" type="submit">Suspend</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.staff.reactivate', $member) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="button success-action" type="submit">Reactivate</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.staff.remove', $member) }}"
                                        onsubmit="return confirm('Remove this person from staff? Their account and inquiry history will be kept, but staff access will be revoked.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button secondary" type="submit">Remove access</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.staff.destroy', $member) }}"
                                        onsubmit="return confirm('Permanently delete this staff account? Follow-up notes will be kept, but the author will no longer be identified.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete account</button>
                                    </form>
                                </div>
                            @else
                                <span class="muted">Protected</span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">No staff accounts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $staff->links() }}
    </div>
</div>
</body>
</html>
