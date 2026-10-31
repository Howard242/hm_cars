<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit ownership guide | HM CARS Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px 50px; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        main { width: min(980px, 100%); margin: 0 auto; }
        header, .panel, .step { margin-bottom: 18px; padding: 22px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; background: #10233f; color: white; }
        header h1 { margin: 0; font-size: 22px; }
        h2 { margin-top: 0; }
        label { display: block; margin: 14px 0 6px; font-weight: 700; }
        input[type=text], input[type=number], textarea, input[type=file] { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        textarea { min-height: 110px; resize: vertical; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; cursor: pointer; }
        .button.secondary { background: white; color: #10233f; }
        .button.danger { border-color: #b42318; background: #b42318; }
        .toolbar, .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        .checkbox { display: flex; align-items: center; gap: 9px; margin-top: 16px; }
        .checkbox input { width: 18px; height: 18px; }
        .checkbox label { margin: 0; }
        .success { margin-bottom: 18px; padding: 12px; border-radius: 6px; background: #dcfce7; color: #166534; }
        .errors { margin-bottom: 18px; padding: 12px; border-radius: 6px; background: #fef3f2; color: #b42318; }
        .step-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
        .step-heading h2 { margin-bottom: 0; }
        .hint { color: #64748b; font-size: 13px; }
        .current-image { display: block; width: min(260px, 100%); max-height: 180px; margin: 12px 0; border-radius: 8px; object-fit: cover; }
        .delete-form { margin-left: auto; }
    </style>
</head>
<body>
<main>
    <header>
        <h1>How to Own a Car · Content editor</h1>
        <div class="toolbar">
            <a class="button secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="button secondary" href="{{ route('admin.ownership-guide.preview') }}">Preview page</a>
        </div>
    </header>

    @if (session('success'))
        <div class="success" role="status">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors" role="alert">
            <strong>Please correct the following:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="panel">
        <h2>Page settings</h2>
        <form method="POST" action="{{ route('admin.ownership-guide.update') }}">
            @csrf
            @method('PUT')
            <label for="title">Page title</label>
            <input id="title" type="text" name="title" value="{{ old('title', $guide->title) }}" required maxlength="180">

            <label for="introduction">Introduction</label>
            <textarea id="introduction" name="introduction" required maxlength="5000">{{ old('introduction', $guide->introduction) }}</textarea>

            <div class="checkbox">
                <input id="is_published" type="checkbox" name="is_published" value="1" @checked(old('is_published', $guide->is_published))>
                <label for="is_published">Publish this guide for customers</label>
            </div>
            <div class="toolbar"><button class="button" type="submit">Save page settings</button></div>
        </form>
    </section>

    <h2>Guide steps</h2>
    @foreach ($guide->steps as $step)
        <section class="step">
            <div class="step-heading">
                <h2>Step: {{ $step->title }}</h2>
                <span class="hint">{{ $step->is_published ? 'Published' : 'Unpublished' }}</span>
            </div>
            <form method="POST" action="{{ route('admin.ownership-guide.steps.update', $step) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label for="title-{{ $step->id }}">Step title</label>
                <input id="title-{{ $step->id }}" type="text" name="title" value="{{ $step->title }}" required maxlength="180">

                <label for="body-{{ $step->id }}">Explanation</label>
                <textarea id="body-{{ $step->id }}" name="body" required maxlength="10000">{{ $step->body }}</textarea>

                <label for="sort-{{ $step->id }}">Display order (lower numbers appear first)</label>
                <input id="sort-{{ $step->id }}" type="number" name="sort_order" value="{{ $step->sort_order }}" min="0" max="1000000" required>

                @if ($step->image_path)
                    <img class="current-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($step->image_path) }}" alt="{{ $step->title }}">
                    <div class="checkbox">
                        <input id="remove-{{ $step->id }}" type="checkbox" name="remove_image" value="1">
                        <label for="remove-{{ $step->id }}">Remove this image</label>
                    </div>
                @endif
                <label for="image-{{ $step->id }}">Replace/add image (JPG, PNG or WebP; up to 5 MB)</label>
                <input id="image-{{ $step->id }}" type="file" name="image" accept="image/jpeg,image/png,image/webp">

                <div class="checkbox">
                    <input id="published-{{ $step->id }}" type="checkbox" name="is_published" value="1" @checked($step->is_published)>
                    <label for="published-{{ $step->id }}">Show this step to customers</label>
                </div>
                <div class="actions"><button class="button" type="submit">Save step</button></div>
            </form>
            <form class="delete-form" method="POST" action="{{ route('admin.ownership-guide.steps.destroy', $step) }}" onsubmit="return confirm('Delete this guide step?')">
                @csrf
                @method('DELETE')
                <button class="button danger" type="submit">Delete step</button>
            </form>
        </section>
    @endforeach

    <section class="panel">
        <h2>Add a step</h2>
        <form method="POST" action="{{ route('admin.ownership-guide.steps.store') }}" enctype="multipart/form-data">
            @csrf
            <label for="new-title">Step title</label>
            <input id="new-title" type="text" name="title" required maxlength="180">

            <label for="new-body">Explanation</label>
            <textarea id="new-body" name="body" required maxlength="10000"></textarea>

            <label for="new-sort">Display order (lower numbers appear first)</label>
            <input id="new-sort" type="number" name="sort_order" value="{{ ($guide->steps->max('sort_order') ?? 0) + 1 }}" min="0" max="1000000" required>

            <label for="new-image">Optional image (JPG, PNG or WebP; up to 5 MB)</label>
            <input id="new-image" type="file" name="image" accept="image/jpeg,image/png,image/webp">

            <div class="checkbox">
                <input id="new-published" type="checkbox" name="is_published" value="1" checked>
                <label for="new-published">Show this step to customers</label>
            </div>
            <div class="toolbar"><button class="button" type="submit">Add step</button></div>
        </form>
    </section>
</main>
</body>
</html>
