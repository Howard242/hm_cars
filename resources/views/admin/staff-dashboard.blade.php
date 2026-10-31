
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HM CARS | Staff Dashboard</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            color: #1b2638;
        }

        header {
            background: #10233f;
            color: white;
            padding: 22px;
        }

        header h1 {
            margin: 0 0 8px;
        }

        header p {
            margin: 8px 0;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            vertical-align: top;
            padding: 13px;
            border-bottom: 1px solid #e5eaf1;
        }

        th {
            background: #eaf0f7;
            font-weight: 600;
        }

        td {
            overflow-wrap: anywhere;
        }

        select,
        button {
            padding: 9px;
            border-radius: 5px;
            font-size: 13px;
        }

        select {
            width: 100%;
            max-width: 180px;
            border: 1px solid #cbd5e1;
            background: white;
        }

        button {
            background: #10233f;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1d416e;
        }

        .logout {
            margin-top: 12px;
        }

        .success {
            background: #d9f7e5;
            color: #14532d;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .error {
            background: #fee4e2;
            color: #b42318;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        .status-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            min-width: 150px;
        }

        .status-actions form {
            width: 100%;
        }

        .status-actions button {
            margin-top: 10px;
        }

        .follow-up-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 9px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            white-space: nowrap;
        }

        .follow-up-btn:hover {
            background: #1d4ed8;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            header {
                padding: 18px;
            }

            header h1 {
                font-size: 22px;
            }

            .container {
                margin: 20px auto;
                padding: 0 10px;
            }

            .card {
                padding: 15px;
            }
        }
        
/* FOLLOW-UP REMINDERS */

.reminder-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.reminder-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
    min-width: 0;
}

.reminder-card h3 {
    margin-top: 0;
}

.reminder-item {
    padding: 12px 0;
    border-bottom: 1px solid #e5eaf1;
    overflow-wrap: anywhere;
}

.reminder-item:last-child {
    border-bottom: none;
}

.reminder-item p {
    margin: 6px 0;
}

.reminder-date {
    font-weight: bold;
}

.overdue-title {
    color: #b42318;
}

.upcoming-title {
    color: #1d4ed8;
}

.reminder-empty {
    color: #64748b;
}

.reminder-link {
    display: inline-block;
    margin-top: 8px;
    color: #1d4ed8;
    font-weight: bold;
    text-decoration: none;
}

.reminder-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .reminder-grid {
        grid-template-columns: 1fr;
    }
}

    </style>
</head>

<body>

<header>
    <h1>HM CARS — Staff Dashboard</h1>

    <p>
        Welcome, {{ auth()->user()->name }}
    </p>

    <p>
        Manage the buyer inquiries assigned to you.
    </p>

    <form method="POST"
          action="{{ route('admin.logout') }}">
        @csrf

        <button class="logout" type="submit">
            Logout
        </button>
    </form>
</header>

<main class="container">

<!-- FOLLOW-UP REMINDERS -->
<section class="reminder-grid" aria-label="Follow-up reminders">

    <!-- OVERDUE FOLLOW-UPS -->
    <div class="reminder-card">

        <h3 class="overdue-title">
            Overdue Follow-Ups ({{ $overdueReminders->count() }})
        </h3>

        @forelse ($overdueReminders as $followUp)

            <div class="reminder-item">

                <strong>
                    Lead #{{ $followUp->inquiry->id }}
                    —
                    {{ $followUp->inquiry->customer_name }}
                </strong>

                <p>
                    <strong>Vehicle:</strong>

                    @if ($followUp->inquiry->vehicle)
                        {{ $followUp->inquiry->vehicle->make }}
                        {{ $followUp->inquiry->vehicle->model }}
                    @else
                        Vehicle unavailable
                    @endif
                </p>

                <p>
                    <strong>Due:</strong>

                    <span class="reminder-date overdue-title">
                        {{ $followUp->next_follow_up_at->format('d M Y, h:i A') }}
                    </span>
                </p>

                <a class="reminder-link"
                   href="{{ route('admin.inquiries.follow-ups.index', $followUp->inquiry) }}">
                    Open Follow-Ups
                </a>

            </div>

        @empty

            <p class="reminder-empty">
                No overdue follow-ups.
            </p>

        @endforelse

    </div>

    <!-- UPCOMING FOLLOW-UPS -->
    <div class="reminder-card">

        <h3 class="upcoming-title">
            Upcoming Follow-Ups ({{ $upcomingReminders->count() }})
        </h3>

        @forelse ($upcomingReminders as $followUp)

            <div class="reminder-item">

                <strong>
                    Lead #{{ $followUp->inquiry->id }}
                    —
                    {{ $followUp->inquiry->customer_name }}
                </strong>

                <p>
                    <strong>Vehicle:</strong>

                    @if ($followUp->inquiry->vehicle)
                        {{ $followUp->inquiry->vehicle->make }}
                        {{ $followUp->inquiry->vehicle->model }}
                    @else
                        Vehicle unavailable
                    @endif
                </p>

                <p>
                    <strong>Scheduled:</strong>

                    <span class="reminder-date">
                        {{ $followUp->next_follow_up_at->format('d M Y, h:i A') }}
                    </span>
                </p>

                <a class="reminder-link"
                   href="{{ route('admin.inquiries.follow-ups.index', $followUp->inquiry) }}">
                    Open Follow-Ups
                </a>

            </div>

        @empty

            <p class="reminder-empty">
                No upcoming follow-ups.
            </p>

        @endforelse

    </div>

</section>

    <div class="card">

        <h2>My Assigned Leads</h2>

        @if (session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error">
                <strong>
                    Please correct the following:
                </strong>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Vehicle</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($inquiries as $inquiry)

                        <tr>

                            <!-- LEAD -->
                            <td>
                                <strong>
                                    #{{ $inquiry->id }}
                                </strong>

                                <br>

                                <small>
                                    {{ $inquiry->created_at?->format('d M Y') }}
                                </small>
                            </td>

                            <!-- VEHICLE -->
                            <td>
                                @if ($inquiry->vehicle)

                                    {{ $inquiry->vehicle->make }}
                                    {{ $inquiry->vehicle->model }}
                                    {{ $inquiry->vehicle->year }}

                                @else
                                    Vehicle unavailable
                                @endif
                            </td>

                            <!-- CUSTOMER -->
                            <td>
                                {{ $inquiry->customer_name }}
                            </td>

                            <!-- CONTACT -->
                            <td>
                                <strong>Phone:</strong>

                                {{ $inquiry->customer_phone ?: 'Not provided' }}

                                <br><br>

                                <strong>Email:</strong>

                                {{ $inquiry->customer_email ?: 'Not provided' }}
                            </td>

                            <!-- MESSAGE -->
                            <td>
                                {{ $inquiry->message ?: 'No message provided.' }}
                            </td>

                            <!-- STATUS AND FOLLOW-UP ACTIONS -->
                            <td>

                                <div class="status-actions">

                                    <!-- UPDATE STATUS -->
                                    <form method="POST"
                                          action="{{ route('admin.inquiries.status', $inquiry) }}">

                                        @csrf
                                        @method('PATCH')

                                        <select name="status"
                                                aria-label="Inquiry status">

                                            @foreach ([
                                                'New',
                                                'Contacted',
                                                'Viewing Scheduled',
                                                'Negotiating',
                                                'Sold'
                                            ] as $status)

                                                <option
                                                    value="{{ $status }}"
                                                    @selected($inquiry->status === $status)>

                                                    {{ $status }}

                                                </option>

                                            @endforeach

                                        </select>

                                        <button type="submit">
                                            Update Status
                                        </button>

                                    </form>

                                    <!-- VIEW FOLLOW-UPS -->
                                    <a
                                        href="{{ route('admin.inquiries.follow-ups.index', $inquiry) }}"
                                        class="follow-up-btn">

                                        View Follow-Ups

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="empty">
                                No leads have been assigned to you yet.

                                <br><br>

                                Please contact your administrator.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination">
            {{ $inquiries->links() }}
        </div>

    </div>

</main>

</body>
</html>
