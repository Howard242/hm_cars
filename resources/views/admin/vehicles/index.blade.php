
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HM CARS | Manage Vehicles</title>

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
            margin: 0 0 8px;
        }

        header p {
            margin: 0;
            color: #d8e2f0;
        }

        .container {
            max-width: 1400px;
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
            background: #10233f;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .button:hover {
            background: #1c3b65;
        }

        .button-edit {
            background: #2563eb;
        }

        .button-edit:hover {
            background: #1d4ed8;
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 15px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .table-wrap {
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        }

        table {
            width: 100%;
            min-width: 950px;
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
        }

        tbody tr:hover {
            background: #fafcff;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .status-published {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-other {
            background: #e2e8f0;
            color: #334155;
        }

        .empty {
            padding: 30px;
            text-align: center;
            color: #64748b;
        }

        .pagination {
            padding: 20px;
        }

        .pagination svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 768px) {
            header {
                padding: 20px;
            }

            .container {
                padding: 0 12px;
            }
        }
        
.button-publish {
    background: #15803d;
}

.button-publish:hover {
    background: #166534;
}

.button-unpublish {
    background: #b45309;
}

.button-unpublish:hover {
    background: #92400e;
}

.button-photos {
    background: #7c3aed;
}

.button-photos:hover {
    background: #6d28d9;
}

.button-promote {
    background: #047857;
}

.button-promote:hover {
    background: #065f46;
}

.button-disabled {
    display: inline-block;
    color: #64748b;
    font-size: 12px;
}


    </style>
</head>

<body>

<header>
    <h1>HM CARS — Vehicle Management</h1>
    <p>The Right Car. The Right Connection.</p>
</header>

<main class="container">

    <div class="top-bar">
        <div>
            <h2>Manage Vehicles</h2>
            <p>View and manage vehicles registered on HM CARS.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('admin.vehicles.create') }}"
               class="button">
                + Add Vehicle
            </a>

            <a href="{{ route('admin.dashboard') }}"
               class="button">
                Back to Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle</th>
                    <th>Year</th>
                    <th>Price (KES)</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Added On</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($vehicles as $vehicle)

                    <tr>
                        <td>
                            #{{ $vehicle->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $vehicle->make }}
                                {{ $vehicle->model }}
                            </strong>
                        </td>

                        <td>
                            {{ $vehicle->year }}
                        </td>

                        <td>
                            {{ number_format((float) $vehicle->price, 2) }}
                        </td>

                        <td>
                            {{ $vehicle->location }}
                        </td>

                        <td>
                            <span class="status
                                @if ($vehicle->status === 'published')
                                    status-published
                                @elseif ($vehicle->status === 'pending')
                                    status-pending
                                @else
                                    status-other
                                @endif
                            ">
                                {{ $vehicle->status }}
                            </span>
                        </td>

                        <td>
                            {{ $vehicle->created_at?->format('d M Y') }}
                        </td>

                        
<td>
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">

        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
           class="button button-edit">
            Edit
        </a>
        
<a href="{{ route('admin.vehicles.images.index', $vehicle) }}"
   class="button button-photos">
    Manage Photos
</a>


        <form method="POST"
              action="{{ route('admin.vehicles.publication', $vehicle) }}"
              style="margin:0;">
            @csrf
            @method('PATCH')

            @if ($vehicle->status === 'published')

                <input type="hidden" name="status" value="pending">

                <button type="submit"
                        class="button button-unpublish"
                        onclick="return confirm('Unpublish this vehicle? It will no longer appear on the public marketplace.');">
                    Unpublish
                </button>

            @else

                <input type="hidden" name="status" value="published">

                <button type="submit"
                        class="button button-publish"
                        onclick="return confirm('Publish this vehicle to the public marketplace?');">
                    Publish
                </button>

            @endif

        </form>

        @if ($vehicle->status === 'published')
            @if ($vehicle->marketing_authorized_at)
                <a href="{{ route('admin.vehicles.promote', $vehicle) }}"
                   class="button button-promote">
                    Promote / Share
                </a>
            @else
                <span class="button-disabled" title="Owner marketing authorization is required.">
                    Promotion needs consent
                </span>
            @endif

            <form method="POST" action="{{ route('admin.vehicles.publication', $vehicle) }}" style="margin:0;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="sold">
                <button type="submit" class="button button-unpublish"
                        onclick="return confirm('Mark this vehicle as sold and remove it from the marketplace?');">
                    Mark sold
                </button>
            </form>

            <form method="POST" action="{{ route('admin.vehicles.publication', $vehicle) }}" style="margin:0;">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="withdrawn">
                <button type="submit" class="button button-unpublish"
                        onclick="return confirm('Withdraw this vehicle and remove it from the marketplace?');">
                    Withdraw
                </button>
            </form>
        @endif

    </div>
</td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="empty">
                            No vehicles have been added yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>

        <div class="pagination">
            {{ $vehicles->links() }}
        </div>

    </div>

</main>

</body>
</html>
