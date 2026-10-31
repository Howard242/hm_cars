<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Review Submission | HM CARS Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        header { padding: 18px 24px; background: #10233f; color: white; }
        header h1 { margin: 0; font-size: 21px; }
        main { width: min(1100px, calc(100% - 32px)); margin: 26px auto; }
        .topbar, .actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .topbar { margin-bottom: 18px; }
        .panel { margin-bottom: 16px; padding: 18px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; }
        h2, h3 { margin: 0 0 12px; }
        p { margin: 7px 0; color: #475569; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; cursor: pointer; }
        .button.secondary { background: white; color: #10233f; }
        .button.approve { background: #166534; border-color: #166534; }
        .button.reject { background: #b42318; border-color: #b42318; }
        .details { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .details strong { display: block; margin-bottom: 3px; color: #64748b; font-size: 12px; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 12px; }
        .gallery img { display: block; width: 100%; aspect-ratio: 4 / 3; border-radius: 6px; object-fit: cover; background: #eaf0f7; }
        .review-actions { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .review-form { display: grid; align-content: start; gap: 10px; }
        label { color: #475569; font-size: 13px; font-weight: 700; }
        textarea { width: 100%; min-height: 100px; resize: vertical; padding: 10px; border: 1px solid #cbd5e1; border-radius: 5px; font: inherit; }
        .feedback { padding: 12px; border-left: 3px solid #64748b; background: #f8fafc; white-space: pre-wrap; }
        .success { padding: 12px; border-radius: 6px; background: #dcfce7; color: #166534; }
        @media (max-width: 760px) { .review-actions { grid-template-columns: 1fr; } .details { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 480px) { main { width: calc(100% - 20px); margin-top: 14px; } header { padding-inline: 16px; } .details { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<header><h1>HM CARS · Submission review</h1></header>
<main>
    <div class="topbar">
        <div>
            <h2>{{ $submission->year }} {{ $submission->make }} {{ $submission->model }}</h2>
            <p>Submission #{{ $submission->id }} · {{ \Illuminate\Support\Str::headline($submission->status) }}</p>
        </div>
        <a class="button secondary" href="{{ route('admin.submissions.index') }}">Back to submissions</a>
    </div>

    @if (session('success'))
        <div class="success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="panel" role="alert">
            <strong>Please correct the following:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="panel">
        <h3>Seller and vehicle details</h3>
        <div class="details">
            <div><strong>Seller</strong>{{ $submission->seller_name }}</div>
            <div><strong>Phone</strong><a href="tel:{{ $submission->seller_phone }}">{{ $submission->seller_phone }}</a></div>
            <div><strong>Email</strong>{{ $submission->seller_email ?: 'Not provided' }}</div>
            <div><strong>Asking price</strong>KSh {{ number_format((float) $submission->price) }}</div>
            <div><strong>Location</strong>{{ $submission->location }}</div>
            <div><strong>Transmission</strong>{{ $submission->transmission ?: 'Not provided' }}</div>
            <div><strong>Fuel</strong>{{ $submission->fuel_type ?: 'Not provided' }}</div>
            <div><strong>Mileage</strong>{{ $submission->mileage_km !== null ? number_format($submission->mileage_km) . ' km' : 'Not provided' }}</div>
            <div><strong>Submitted</strong>{{ $submission->created_at?->format('d M Y, H:i') }}</div>
            <div>
                <strong>Brokerage agreement</strong>
                @if ($submission->terms_accepted_at)
                    Accepted {{ $submission->terms_accepted_at->format('d M Y, H:i') }} (version {{ $submission->terms_version }})
                @else
                    No recorded acceptance
                @endif
            </div>
            <div>
                <strong>Marketing authorization</strong>
                @if ($submission->marketing_authorized_at)
                    Authorized {{ $submission->marketing_authorized_at->format('d M Y, H:i') }}
                @else
                    Not authorized
                @endif
            </div>
        </div>
        @if ($submission->description)
            <h3 style="margin-top: 18px;">Description</h3>
            <p>{{ $submission->description }}</p>
        @endif
        @if ($submission->admin_feedback)
            <h3 style="margin-top: 18px;">Last feedback</h3>
            <p class="feedback">{{ $submission->admin_feedback }}</p>
        @endif
        @if ($submission->seller_response)
            <h3 style="margin-top: 18px;">Seller response</h3>
            <p class="feedback">{{ $submission->seller_response }}</p>
        @endif
    </section>

    <section class="panel">
        <h3>Submitted photos</h3>
        <div class="gallery">
            @foreach ($submission->images as $image)
                <a href="{{ route('admin.submissions.photos.show', ['submission' => $submission, 'image' => $image]) }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ route('admin.submissions.photos.show', ['submission' => $submission, 'image' => $image]) }}" alt="Submitted vehicle photo {{ $loop->iteration }}" loading="lazy">
                </a>
            @endforeach
        </div>
    </section>

    @if (in_array($submission->status, ['pending_review', 'more_information_required'], true))
        <section class="review-actions" aria-label="Review actions">
            <form class="panel review-form" method="POST" action="{{ route('admin.submissions.approve', $submission) }}">
                @csrf
                <h3>Approve and publish</h3>
                <label for="approve-feedback">Optional seller feedback</label>
                <textarea id="approve-feedback" name="admin_feedback" maxlength="5000">{{ old('admin_feedback') }}</textarea>
                <button class="button approve" type="submit">Approve submission</button>
            </form>

            <form class="panel review-form" method="POST" action="{{ route('admin.submissions.request-information', $submission) }}">
                @csrf
                <h3>Request more information</h3>
                <label for="information-feedback">What should the seller clarify?</label>
                <textarea id="information-feedback" name="admin_feedback" required minlength="5" maxlength="5000">{{ old('admin_feedback') }}</textarea>
                <button class="button secondary" type="submit">Request information</button>
            </form>

            <form class="panel review-form" method="POST" action="{{ route('admin.submissions.reject', $submission) }}">
                @csrf
                <h3>Reject submission</h3>
                <label for="reject-feedback">Reason for rejection</label>
                <textarea id="reject-feedback" name="admin_feedback" required minlength="5" maxlength="5000">{{ old('admin_feedback') }}</textarea>
                <button class="button reject" type="submit">Reject submission</button>
            </form>
        </section>
    @elseif ($submission->status === 'approved' && $submission->approvedVehicle)
        <p><a class="button approve" href="{{ route('vehicles.show', $submission->approvedVehicle) }}" target="_blank" rel="noopener noreferrer">View published marketplace listing</a></p>
    @endif
</main>
</body>
</html>