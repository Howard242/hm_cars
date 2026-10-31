<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seller Agreement | HM CARS Admin</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px 50px; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        main { width: min(980px, 100%); margin: 0 auto; }
        header, .panel, .clause { margin-bottom: 18px; padding: 22px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; background: #10233f; color: white; }
        header h1 { margin: 0; font-size: 22px; }
        h2 { margin-top: 0; }
        label { display: block; margin: 14px 0 6px; font-weight: 700; }
        input[type=text], textarea { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        textarea { min-height: 150px; resize: vertical; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; cursor: pointer; }
        .button.secondary { background: white; color: #10233f; }
        .button.danger { border-color: #b42318; background: #b42318; }
        .toolbar, .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 16px; }
        .success { margin-bottom: 18px; padding: 12px; border-radius: 6px; background: #dcfce7; color: #166534; }
        .errors { margin-bottom: 18px; padding: 12px; border-radius: 6px; background: #fef3f2; color: #b42318; }
        .hint { color: #64748b; font-size: 13px; }
        details { padding: 14px 0; border-top: 1px solid #e2e8f0; }
        summary { cursor: pointer; font-weight: 700; }
        .version-content { padding: 10px 0 0 18px; }
        .version-content li { margin-bottom: 10px; }
        .clause-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>Seller Brokerage Agreement</h1>
            <span>Current published version: {{ $terms->version }}</span>
        </div>
        <a class="button secondary" href="{{ route('admin.dashboard') }}">Dashboard</a>
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
        <h2>Edit agreement</h2>
        <p>Saving creates a new version. Earlier versions are retained so each seller’s recorded acceptance continues to refer to the terms they saw.</p>
        <form method="POST" action="{{ route('admin.seller-terms.update') }}" id="terms-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="current_version" value="{{ $terms->version }}">

            <label for="title">Agreement title</label>
            <input id="title" type="text" name="title" value="{{ old('title', $terms->title) }}" required maxlength="180">

            <label for="introduction">Introduction</label>
            <textarea id="introduction" name="introduction" required maxlength="2000">{{ old('introduction', $terms->introduction) }}</textarea>

            <h2>Clauses</h2>
            <p class="hint">Text is displayed as plain text; HTML is not allowed. Add, remove, or reorder clauses as needed.</p>
            <div id="clauses">
                @foreach (old('clauses', $terms->clauses) as $index => $clause)
                    <section class="clause">
                        <div class="clause-heading">
                            <h3>Clause</h3>
                            <button class="button danger remove-clause" type="button">Remove clause</button>
                        </div>
                        <label for="clause-title-{{ $index }}">Heading</label>
                        <input id="clause-title-{{ $index }}" type="text" name="clauses[{{ $index }}][title]" value="{{ $clause['title'] ?? '' }}" required maxlength="180">
                        <label for="clause-body-{{ $index }}">Terms</label>
                        <textarea id="clause-body-{{ $index }}" name="clauses[{{ $index }}][body]" required maxlength="5000">{{ $clause['body'] ?? '' }}</textarea>
                    </section>
                @endforeach
            </div>

            <template id="clause-template">
                <section class="clause">
                    <div class="clause-heading">
                        <h3>Clause</h3>
                        <button class="button danger remove-clause" type="button">Remove clause</button>
                    </div>
                    <label>Heading</label>
                    <input type="text" data-name="title" required maxlength="180">
                    <label>Terms</label>
                    <textarea data-name="body" required maxlength="5000"></textarea>
                </section>
            </template>

            <div class="toolbar">
                <button class="button secondary" type="button" id="add-clause">Add clause</button>
                <button class="button" type="submit">Publish new version</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Previous versions</h2>
        @foreach ($versions as $version)
            <details>
                <summary>Version {{ $version->version }} — {{ $version->created_at->format('d M Y, H:i') }}{{ $version->id === $terms->id ? ' (current)' : '' }}</summary>
                <div class="version-content">
                    <h3>{{ $version->title }}</h3>
                    <p>{{ $version->introduction }}</p>
                    <ol>
                        @foreach ($version->clauses as $clause)
                            <li><strong>{{ $clause['title'] }}:</strong> {{ $clause['body'] }}</li>
                        @endforeach
                    </ol>
                </div>
            </details>
        @endforeach
    </section>
</main>
<script>
    const clauses = document.getElementById('clauses');
    const template = document.getElementById('clause-template');
    let nextClauseIndex = clauses.querySelectorAll('.clause').length;

    document.getElementById('add-clause').addEventListener('click', () => {
        const clause = template.content.cloneNode(true);
        const section = clause.querySelector('.clause');
        section.querySelectorAll('[data-name]').forEach((field) => {
            const name = field.dataset.name;
            field.name = `clauses[${nextClauseIndex}][${name}]`;
            field.id = `clause-${name}-${nextClauseIndex}`;
            field.previousElementSibling.htmlFor = field.id;
        });
        nextClauseIndex += 1;
        clauses.appendChild(clause);
    });

    clauses.addEventListener('click', (event) => {
        if (event.target.matches('.remove-clause')) {
            event.target.closest('.clause').remove();
        }
    });
</script>
</body>
</html>
