
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HM CARS | Admin Dashboard</title>

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
            padding: 22px 30px;
        }

        header h1 {
            margin: 0 0 6px;
        }

        header p {
            margin: 0;
            color: #d8e2f0;
        }

        .container {
            max-width: 1500px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .top-bar h2 {
            margin: 0;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background: #10233f;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .button:hover {
            background: #1c3b65;
        }

        .logout-form {
            display: inline;
        }

        .success {
            padding: 14px;
            margin-bottom: 20px;
            background: #d9f7e5;
            color: #14532d;
            border: 1px solid #a7e8bf;
            border-radius: 7px;
        }

        .error-box {
            padding: 14px;
            margin-bottom: 20px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 7px;
        }

        .table-wrap {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #e5eaf1;
        }

        th {
            background: #eaf0f7;
            color: #10233f;
            white-space: nowrap;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        select {
            width: 100%;
            min-width: 125px;
            padding: 9px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            background: white;
        }

        .save {
            margin-top: 8px;
            padding: 8px 10px;
            background: #10233f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .save:hover {
            background: #1c3b65;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #64748b;
        }

        .pagination {
            padding: 20px;
        }

        .pagination nav {
            display: flex;
            justify-content: center;
        }

        .pagination svg {
            width: 18px;
            height: 18px;
        }

        .pagination p {
            margin: 0 0 10px;
        }

        .message {
            min-width: 180px;
            max-width: 300px;
            white-space: normal;
            overflow-wrap: anywhere;
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
            padding: 9px 12px;
            background: #10233f;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        .follow-up-btn {
            display: inline-block;
            padding: 9px 12px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            white-space: nowrap;
        }

        .follow-up-btn:hover {
            background: #1d4ed8;
        }

        @media (max-width: 600px) {
            header {
                padding: 20px;
            }

            .container {
                padding: 0 12px;
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

.dashboard-heading,
.section-heading {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 16px;
    margin-bottom: 18px;
}

.dashboard-heading h2,
.section-heading h2 {
    margin: 0 0 4px;
}

.dashboard-heading p,
.section-heading p {
    margin: 0;
    color: #64748b;
}

.quick-actions,
.dashboard-section,
.stat-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.quick-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px;
    margin-bottom: 22px;
}

.quick-actions strong {
    margin-right: 6px;
}

.quick-action {
    display: inline-flex;
    align-items: center;
    min-height: 40px;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    color: #10233f;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
}

.quick-action.primary {
    background: #10233f;
    border-color: #10233f;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}

.stat-card {
    min-width: 0;
    padding: 16px;
}

.stat-label {
    color: #64748b;
    font-size: 13px;
    font-weight: 700;
}

.stat-value {
    display: block;
    margin: 5px 0 2px;
    color: #10233f;
    font-size: 28px;
    line-height: 1.2;
    font-weight: 800;
}

.stat-note {
    color: #64748b;
    font-size: 12px;
}

.recent-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.dashboard-section {
    min-width: 0;
    padding: 18px;
}

.dashboard-section .section-heading {
    align-items: center;
    margin-bottom: 10px;
}

.dashboard-section .section-heading h2 {
    font-size: 18px;
}

.section-link,
.item-action {
    color: #1d4ed8;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    padding: 12px 0;
    border-top: 1px solid #e5eaf1;
}

.recent-item:first-of-type {
    border-top: 0;
}

.recent-item-body {
    flex: 1;
    min-width: 0;
}

.recent-item-title {
    display: block;
    color: #10233f;
    font-weight: 750;
    overflow-wrap: anywhere;
}

.recent-item-meta,
.recent-item-message {
    display: block;
    margin-top: 3px;
    color: #64748b;
    font-size: 12px;
    overflow-wrap: anywhere;
}

.recent-vehicle-image,
.recent-vehicle-placeholder {
    flex: 0 0 64px;
    width: 64px;
    height: 48px;
    border-radius: 5px;
    object-fit: cover;
    background: #eaf0f7;
}

.recent-vehicle-placeholder {
    display: grid;
    place-items: center;
    color: #10233f;
    font-size: 13px;
    font-weight: 800;
}

.recent-item-actions {
    display: flex;
    flex: 0 0 auto;
    gap: 10px;
}

.status-label {
    display: inline-block;
    padding: 3px 7px;
    border-radius: 4px;
    background: #eaf0f7;
    color: #334155;
    font-size: 11px;
    font-weight: 750;
}

.dashboard-empty {
    padding: 16px 0 4px;
    color: #64748b;
    font-size: 14px;
}

@media (max-width: 900px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 700px) {
    .recent-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 520px) {
    .dashboard-heading {
        align-items: start;
        flex-direction: column;
    }

    .quick-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .quick-actions strong {
        margin: 0 0 4px;
    }

    .quick-action {
        justify-content: center;
    }

    .recent-item {
        align-items: start;
        flex-wrap: wrap;
    }

    .recent-item-actions {
        padding-left: 76px;
    }
}

    </style>
</head>

<body>

<header>
    <h1>HM CARS</h1>
    <p>Admin Dashboard — The Right Car. The Right Connection.</p>
</header>

<div class="container">

    <section class="dashboard-heading">
        <div>
            <h2>Operations Overview</h2>
            <p>Vehicle supply, customer interest, and follow-up activity.</p>
        </div>
        <span>{{ now()->format('D, d M Y') }}</span>
    </section>

    <nav class="quick-actions" aria-label="Dashboard quick actions">
        <strong>Quick actions</strong>
        <a class="quick-action primary" href="{{ route('admin.vehicles.create') }}">Add a vehicle</a>
        <a class="quick-action" href="{{ route('admin.home-slides.index') }}">Manage homepage slider</a>
        <a class="quick-action" href="{{ route('admin.vehicles.index') }}">Manage listings</a>
        <a class="quick-action" href="{{ route('admin.staff.index') }}">Manage Staff</a>
        <a class="quick-action" href="{{ route('admin.submissions.index') }}">Review seller submissions</a>
        <a class="quick-action" href="{{ route('admin.marketing.index') }}">Marketing performance</a>
        <a class="quick-action" href="{{ route('admin.ownership-guide.edit') }}">Edit ownership guide</a>
        <a class="quick-action" href="{{ route('admin.seller-terms.edit') }}">Edit seller agreement</a>
        <a class="quick-action" href="#inquiries">Review inquiries</a>
    </nav>

    <section class="stats-grid" aria-label="Business statistics">
        <article class="stat-card">
            <span class="stat-label">Total vehicles</span>
            <strong class="stat-value">{{ number_format($totalVehicles) }}</strong>
            <span class="stat-note">All listings in the system</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Available vehicles</span>
            <strong class="stat-value">{{ number_format($availableVehicles) }}</strong>
            <span class="stat-note">Published and not marked sold</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Sold vehicles</span>
            <strong class="stat-value">{{ number_format($soldVehicles) }}</strong>
            <span class="stat-note">Distinct vehicles with a sold inquiry</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Recently added</span>
            <strong class="stat-value">{{ number_format($recentVehicleCount) }}</strong>
            <span class="stat-note">Added in the last 30 days</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Total inquiries</span>
            <strong class="stat-value">{{ number_format($totalInquiries) }}</strong>
            <span class="stat-note">All customer inquiries</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">New inquiries</span>
            <strong class="stat-value">{{ number_format($newInquiries) }}</strong>
            <span class="stat-note">Awaiting first status update</span>
        </article>
        <article class="stat-card">
            <span class="stat-label">Awaiting follow-up</span>
            <strong class="stat-value">{{ number_format($awaitingFollowUp) }}</strong>
            <span class="stat-note">No future reminder is scheduled</span>
        </article>
    </section>

    <section class="recent-grid" aria-label="Recent business activity">
        <div class="dashboard-section">
            <div class="section-heading">
                <h2>Recent Customer Inquiries</h2>
                <a class="section-link" href="#inquiries">View all</a>
            </div>

            @forelse ($recentInquiries as $recentInquiry)
                <article class="recent-item">
                    <div class="recent-item-body">
                        <strong class="recent-item-title">
                            {{ $recentInquiry->customer_name }}
                            <span class="status-label">{{ $recentInquiry->status }}</span>
                        </strong>
                        <span class="recent-item-meta">
                            @if ($recentInquiry->vehicle)
                                {{ $recentInquiry->vehicle->year }}
                                {{ $recentInquiry->vehicle->make }}
                                {{ $recentInquiry->vehicle->model }}
                            @else
                                Vehicle unavailable
                            @endif
                            · {{ $recentInquiry->created_at?->format('d M Y, H:i') }}
                        </span>
                        <span class="recent-item-message">{{ \Illuminate\Support\Str::limit($recentInquiry->message, 90) }}</span>
                    </div>
                    <div class="recent-item-actions">
                        <a class="item-action" href="{{ route('admin.inquiries.follow-ups.index', $recentInquiry) }}">Follow-ups</a>
                    </div>
                </article>
            @empty
                <p class="dashboard-empty">No customer inquiries yet.</p>
            @endforelse
        </div>

        <div class="dashboard-section">
            <div class="section-heading">
                <h2>Recent Vehicle Listings</h2>
                <a class="section-link" href="{{ route('admin.vehicles.index') }}">View all</a>
            </div>

            @forelse ($recentVehicles as $recentVehicle)
                <article class="recent-item">
                    @if ($recentVehicle->primaryImage)
                        <img
                            class="recent-vehicle-image"
                            src="{{ route('admin.vehicles.images.photo', ['vehicle' => $recentVehicle->id, 'image' => $recentVehicle->primaryImage->id]) }}"
                            alt=""
                            loading="lazy"
                        >
                    @else
                        <span class="recent-vehicle-placeholder" aria-hidden="true">
                            {{ strtoupper(substr($recentVehicle->make, 0, 1) . substr($recentVehicle->model, 0, 1)) }}
                        </span>
                    @endif
                    <div class="recent-item-body">
                        <strong class="recent-item-title">
                            {{ $recentVehicle->year }} {{ $recentVehicle->make }} {{ $recentVehicle->model }}
                        </strong>
                        <span class="recent-item-meta">
                            KSh {{ number_format((float) $recentVehicle->price) }} · {{ $recentVehicle->location }}
                        </span>
                        <span class="status-label">
                            @if ($recentVehicle->sold_inquiries_count > 0)
                                Sold
                            @elseif ($recentVehicle->status === 'published')
                                Available
                            @else
                                {{ ucfirst($recentVehicle->status) }}
                            @endif
                        </span>
                    </div>
                    <div class="recent-item-actions">
                        <a class="item-action" href="{{ route('admin.vehicles.edit', $recentVehicle) }}">Edit</a>
                        <a class="item-action" href="{{ route('admin.vehicles.images.index', $recentVehicle) }}">Photos</a>
                    </div>
                </article>
            @empty
                <p class="dashboard-empty">No vehicle listings yet.</p>
            @endforelse
        </div>
    </section>

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

    <div class="top-bar">
        <div>
            <h2>Buyer Inquiries</h2>
            <p>Review leads, update their status and manage assignments.</p>
        </div>

        <div>
            @if (auth()->user()->is_admin)
                <a href="{{ route('admin.submissions.index') }}" class="button">
                    Vehicle Submissions
                </a>
                <a href="{{ route('admin.staff.index') }}" class="button">
                    Manage Staff
                </a>
            @endif

            <form method="POST"
                  action="{{ route('admin.logout') }}"
                  class="logout-form">
                @csrf

                <button type="submit" class="button">
                    Logout
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <strong>Please correct the following:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-wrap" id="inquiries">
        <table>
            <thead>
                <tr>
                    <th>Lead</th>
                    <th>Vehicle</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Assigned Staff</th>
                    
                </tr>
            </thead>

            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>
                            #{{ $inquiry->id }}
                            <br>
                            <small>
                                {{ $inquiry->created_at?->format('d M Y, H:i') }}
                            </small>
                        </td>
                        



                        <td>
                            @if ($inquiry->vehicle)
                                <a href="{{ route('vehicles.show', $inquiry->vehicle) }}"
                                   target="_blank">
                                    {{ $inquiry->vehicle->make }}
                                    {{ $inquiry->vehicle->model }}
                                    {{ $inquiry->vehicle->year }}
                                </a>
                            @else
                                Vehicle unavailable
                            @endif
                        </td>

                        <td>
                            {{ $inquiry->customer_name }}
                        </td>

                        <td>
                            <strong>Phone:</strong>
                            {{ $inquiry->customer_phone ?: 'Not provided' }}

                            <br><br>

                            <strong>Email:</strong>
                            {{ $inquiry->customer_email ?: 'Not provided' }}
                        </td>

                        <td class="message">
                            {{ $inquiry->message ?: 'No message provided.' }}
                        </td>
                        <!-- STATUS AND FOLLOW-UP ACTIONS -->
                        <td>
                            <div class="status-actions">
                                <form method="POST"
                                      action="{{ route('admin.inquiries.status', $inquiry) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="status"
                                            aria-label="Inquiry status #{{ $inquiry->id }}">
                                        @foreach ([
                                            'New',
                                            'Contacted',
                                            'Viewing Scheduled',
                                            'Negotiating',
                                            'Sold'
                                        ] as $status)
                                            <option value="{{ $status }}"
                                                @selected($inquiry->status === $status)>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit">Update Status</button>
                                </form>

                                <a href="{{ route('admin.inquiries.follow-ups.index', $inquiry) }}"
                                   class="follow-up-btn">
                                    View Follow-Ups
                                </a>
                            </div>
                        </td>


                        <td>
                            @if (auth()->user()->is_admin)
                                <form method="POST"
                                      action="{{ route('admin.inquiries.assign', $inquiry) }}">
                                    @csrf
                                    @method('PATCH')

                                    <select name="assigned_to_user_id"
                                            aria-label="Assigned staff">
                                        <option value="">Unassigned</option>

                                        @foreach ($staff as $member)
                                            <option value="{{ $member->id }}"
                                                @selected((string) $inquiry->assigned_to_user_id === (string) $member->id)>
                                                {{ $member->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit">Assign</button>
                                </form>
                            @else
                                {{ $inquiry->assignedStaff?->name ?? 'Unassigned' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            No inquiries yet. New buyer inquiries will appear here.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">
            {{ $inquiries->links() }}
        </div>
    </div>

</div>

</body>
</html>
