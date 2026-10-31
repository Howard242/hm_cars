
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lead Follow-Ups | HM CARS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
            color: #17243a;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 35px auto;
            padding: 0 16px;
        }
        .card {
            background: white;
            padding: 24px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px #00000010;
        }
        input, select, textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 11px;
            margin: 8px 0 16px;
            border: 1px solid #ccd3dd;
            border-radius: 6px;
        }
        button {
            background: #10233f;
            color: white;
            padding: 12px 20px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
        .entry {
            border-bottom: 1px solid #e4e7ed;
            padding: 14px 0;
        }
        .muted { color: #667085; }
        .error { color: #b42318; }
        .success { color: #067647; }
        a { color: #174ea6; }
    </style>
</head>
<body>
<div class="container">

    <p>
        <a href="{{ route('admin.dashboard') }}">
            &larr; Back to Dashboard
        </a>
    </p>

    <h1>HM CARS — Lead Follow-Ups</h1>

    <div class="card">
        <h2>{{ $inquiry->customer_name }}</h2>

        <p>
            <strong>Phone:</strong>
            {{ $inquiry->customer_phone }}
        </p>

        <p>
            <strong>Vehicle:</strong>
            {{ $inquiry->vehicle?->make }}
            {{ $inquiry->vehicle?->model }}
            {{ $inquiry->vehicle?->year }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $inquiry->status }}
        </p>
    </div>

    <div class="card">
        <h2>Record a Follow-Up</h2>

        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.inquiries.follow-ups.store', $inquiry) }}">
            @csrf

            <label for="contact_method">Contact Method</label>
            <select id="contact_method"
                    name="contact_method"
                    required>
                <option value="">Select method</option>

                @foreach(['Phone Call', 'WhatsApp', 'SMS', 'Email', 'In Person'] as $method)
                    <option value="{{ $method }}"
                        @selected(old('contact_method') === $method)>
                        {{ $method }}
                    </option>
                @endforeach
            </select>

            <label for="notes">Conversation Notes</label>
            <textarea id="notes"
                      name="notes"
                      rows="5"
                      maxlength="5000"
                      required>{{ old('notes') }}</textarea>

            <label for="next_follow_up_at">
                Next Follow-Up (optional)
            </label>
            <input type="datetime-local"
                   id="next_follow_up_at"
                   name="next_follow_up_at"
                   value="{{ old('next_follow_up_at') }}">

            <button type="submit">Save Follow-Up</button>
        </form>
    </div>

    <div class="card">
        <h2>Follow-Up History</h2>

        @forelse($followUps as $followUp)
            <div class="entry">
                <p>
                    <strong>{{ $followUp->contact_method }}</strong>
                    —
                    {{ $followUp->created_at->format('d M Y, h:i A') }}
                </p>

                <p>{{ $followUp->notes }}</p>

                <p class="muted">
                    Recorded by:
                    {{ $followUp->staff?->name ?? 'Former staff member' }}
                </p>

                @if($followUp->next_follow_up_at)
                    <p>
                        <strong>Next follow-up:</strong>
                        {{ $followUp->next_follow_up_at->format('d M Y, h:i A') }}
                    </p>
                @endif
            </div>
        @empty
            <p class="muted">
                No follow-ups recorded for this lead yet.
            </p>
        @endforelse

        {{ $followUps->links() }}
    </div>

</div>
</body>
</html>
