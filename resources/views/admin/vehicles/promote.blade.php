<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Promote {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} | HM CARS</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px 16px 48px; background: #f3f6fa; color: #1b2638; font-family: Arial, sans-serif; }
        main { width: min(1120px, 100%); margin: 0 auto; }
        header, .panel, .photo { margin-bottom: 18px; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; background: #10233f; color: white; }
        header h1 { margin: 0 0 6px; font-size: 22px; }
        header p { margin: 0; color: #d8e2f0; }
        h2 { margin: 0 0 12px; }
        p { line-height: 1.55; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 9px 13px; border: 1px solid #10233f; border-radius: 6px; background: #10233f; color: white; text-decoration: none; font-weight: 700; cursor: pointer; font: inherit; }
        .button.secondary { background: white; color: #10233f; }
        .button.facebook { background: #1877f2; border-color: #1877f2; }
        .button.whatsapp { background: #128c7e; border-color: #128c7e; }
        .button.instagram { background: #b83280; border-color: #b83280; }
        .button.tiktok { background: #111827; border-color: #111827; }
        .platforms, .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .platform-form { margin: 0; }
        .note { padding: 12px; border-left: 4px solid #2563eb; background: #eff6ff; color: #1e3a8a; }
        .success { padding: 12px; border-radius: 6px; background: #dcfce7; color: #166534; }
        .caption { width: 100%; min-height: 180px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; line-height: 1.5; }
        .photos { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 14px; }
        .photo { padding: 12px; margin: 0; }
        .photo img { display: block; width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: 6px; background: #e2e8f0; }
        .photo p { margin: 8px 0; color: #64748b; font-size: 13px; }
        .photo .actions { display: grid; grid-template-columns: 1fr; }
        .photo .button { width: 100%; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 700px; }
        th, td { padding: 11px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #f8fafc; }
        .tracked-link { max-width: 260px; overflow-wrap: anywhere; }
        #download-status { min-height: 22px; color: #166534; }
        @media (max-width: 640px) { body { padding: 12px 10px 32px; } header, .panel { padding: 16px; } }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>Promote vehicle</h1>
            <p>{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} · KSh {{ number_format((float) $vehicle->price) }}</p>
        </div>
        <a class="button secondary" href="{{ route('admin.vehicles.index') }}">Back to vehicles</a>
    </header>

    @if (session('success'))
        <p class="success" role="status">{{ session('success') }}</p>
    @endif

    <section class="panel">
        <h2>Share this public listing</h2>
        <p class="note">Facebook and WhatsApp open a share composer with a campaign-tracked listing link. Instagram and TikTok do not support general website links that publish posts: prepare the branded image files below, copy the caption, then upload and publish manually.</p>
        <div class="platforms">
            @foreach (['facebook' => 'Share on Facebook', 'whatsapp' => 'Share on WhatsApp', 'instagram' => 'Prepare Instagram post', 'tiktok' => 'Prepare TikTok photo post'] as $platform => $label)
                <form class="platform-form" method="POST" action="{{ route('admin.vehicles.promotions.store', $vehicle) }}" @if (in_array($platform, ['facebook', 'whatsapp'], true)) target="_blank" rel="noopener" @endif>
                    @csrf
                    <input type="hidden" name="platform" value="{{ $platform }}">
                    <input type="hidden" name="caption" value="">
                    <button class="button {{ $platform }}" type="submit">{{ $label }}</button>
                </form>
            @endforeach
        </div>
        @if (session('prepared_platform'))
            @if (session('prepared_platform') === 'instagram')
                <p><a class="button instagram" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">Open Instagram</a></p>
            @else
                <p><a class="button tiktok" href="https://www.tiktok.com/upload" target="_blank" rel="noopener noreferrer">Open TikTok upload</a></p>
            @endif
        @endif
    </section>

    <section class="panel">
        <h2>Marketing caption</h2>
        <label for="caption">Review or edit the caption before posting</label>
        <textarea class="caption" id="caption" maxlength="2000">{{ old('caption', session('prepared_caption', $caption)) }}</textarea>
        <div class="actions">
            <button type="button" class="button secondary" id="copy-caption">Copy caption</button>
            <a class="button secondary" href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer">Preview public listing</a>
        </div>
        <p id="download-status" role="status" aria-live="polite"></p>
    </section>

    <section class="panel">
        <h2>Branded photos for Instagram and TikTok</h2>
        <p>Download portrait copies with HM CARS branding and the configured phone number. The listing's original photos are not changed. TikTok can use these as a photo slideshow; upload each downloaded slide in order.</p>
        @if ($vehicle->images->isEmpty())
            <p>No photos are available yet. Manage vehicle photos before preparing Instagram or TikTok content.</p>
        @else
            <div class="photos">
                @foreach ($vehicle->images as $image)
                    <article class="photo">
                        <img src="{{ asset('storage/'.$image->image_path) }}" alt="Vehicle photo {{ $loop->iteration }}" loading="lazy">
                        <p>Photo {{ $loop->iteration }}{{ $image->is_primary ? ' · Primary' : '' }}</p>
                        <div class="actions">
                            <button type="button" class="button instagram download-photo"
                                    data-image="{{ asset('storage/'.$image->image_path) }}"
                                    data-format="instagram"
                                    data-index="{{ $loop->iteration }}">Download Instagram image</button>
                            <button type="button" class="button tiktok download-photo"
                                    data-image="{{ asset('storage/'.$image->image_path) }}"
                                    data-format="tiktok"
                                    data-index="{{ $loop->iteration }}">Download TikTok vertical slide</button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="panel">
        <h2>Promotion history and results</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Platform</th><th>Promoted</th><th>Admin</th><th>Visits</th><th>Inquiries</th><th>Tracked listing</th></tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        @php
                            $campaign = 'vehicle-'.$vehicle->id.'-promotion-'.$promotion->id;
                            $trackedUrl = $publicUrl.'?'.http_build_query([
                                'utm_source' => $promotion->platform,
                                'utm_medium' => 'social',
                                'utm_campaign' => $campaign,
                            ]);
                        @endphp
                        <tr>
                            <td>{{ \Illuminate\Support\Str::headline($promotion->platform) }}</td>
                            <td>{{ $promotion->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $promotion->promoter?->name ?? 'Former admin' }}</td>
                            <td>{{ $promotion->visits_count }}</td>
                            <td>{{ $promotion->inquiries_count }}</td>
                            <td class="tracked-link"><a href="{{ $trackedUrl }}" target="_blank" rel="noopener noreferrer">Open tracked link</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No promotions have been recorded for this vehicle.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $promotions->links() }}
    </section>
</main>
<script>
    const status = document.getElementById('download-status');
    const phone = @json($contactPhone);
    const vehicleTitle = @json($vehicle->year.' '.$vehicle->make.' '.$vehicle->model);
    const vehiclePrice = @json('KSh '.number_format((float) $vehicle->price));
    const vehicleLocation = @json($vehicle->location);

    document.querySelectorAll('.platform-form').forEach((form) => {
        form.addEventListener('submit', () => {
            form.querySelector('input[name="caption"]').value =
                document.getElementById('caption').value;
        });
    });

    document.getElementById('copy-caption').addEventListener('click', async () => {
        const caption = document.getElementById('caption').value;

        try {
            await navigator.clipboard.writeText(caption);
            status.textContent = 'Caption copied to clipboard.';
        } catch {
            const field = document.getElementById('caption');
            field.removeAttribute('readonly');
            field.select();
            const copied = document.execCommand('copy');
            field.setAttribute('readonly', '');
            status.textContent = copied
                ? 'Caption copied to clipboard.'
                : 'Copy was blocked. Select the caption and copy it manually.';
        }
    });

    function loadImage(source) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = () => reject(new Error('The vehicle photo could not be loaded.'));
            image.src = source;
        });
    }

    function drawPromoImage(image, format) {
        const width = 1080;
        const height = format === 'tiktok' ? 1920 : 1350;
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const context = canvas.getContext('2d');

        if (!context) {
            throw new Error('Your browser does not support branded photo downloads.');
        }

        context.fillStyle = '#f3f6fa';
        context.fillRect(0, 0, width, height);
        context.fillStyle = '#10233f';
        context.fillRect(0, 0, width, 148);
        context.fillStyle = '#ffffff';
        context.fillRect(42, 34, 78, 78);
        context.fillStyle = '#10233f';
        context.font = 'bold 36px Arial';
        context.fillText('HM', 52, 83);
        context.fillStyle = '#ffffff';
        context.font = 'bold 46px Arial';
        context.fillText('HM CARS', 145, 75);
        context.font = '24px Arial';
        context.fillText('The Right Car. The Right Connection.', 145, 112);

        const footerHeight = format === 'tiktok' ? 500 : 300;
        const imageArea = {
            x: 48,
            y: 180,
            width: width - 96,
            height: height - footerHeight - 210,
        };
        const scale = Math.min(imageArea.width / image.naturalWidth, imageArea.height / image.naturalHeight);
        const drawWidth = image.naturalWidth * scale;
        const drawHeight = image.naturalHeight * scale;
        context.drawImage(
            image,
            imageArea.x + (imageArea.width - drawWidth) / 2,
            imageArea.y + (imageArea.height - drawHeight) / 2,
            drawWidth,
            drawHeight
        );

        const footerY = height - footerHeight;
        context.fillStyle = '#10233f';
        context.fillRect(0, footerY, width, footerHeight);
        context.fillStyle = '#ffffff';
        context.font = 'bold 40px Arial';
        context.fillText(vehicleTitle, 48, footerY + 72, width - 96);
        context.font = 'bold 38px Arial';
        context.fillText(vehiclePrice, 48, footerY + 132, width - 96);
        context.font = '30px Arial';
        context.fillText(vehicleLocation, 48, footerY + 185, width - 96);
        context.font = 'bold 28px Arial';
        context.fillText(phone ? 'Call / WhatsApp ' + phone : 'View this car at hmcars.co.ke', 48, footerY + 245, width - 96);
        context.font = '23px Arial';
        context.fillText('Visit the HM CARS website for details', 48, height - 34, width - 96);

        return canvas;
    }

    document.querySelectorAll('.download-photo').forEach((button) => {
        button.addEventListener('click', async () => {
            button.disabled = true;
            status.textContent = 'Preparing branded photo…';

            try {
                const image = await loadImage(button.dataset.image);
                const canvas = drawPromoImage(image, button.dataset.format);
                const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.92));

                if (!blob) {
                    throw new Error('The branded photo could not be generated.');
                }

                const downloadUrl = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = 'hm-cars-' + button.dataset.format + '-' + button.dataset.index + '.jpg';
                link.click();
                URL.revokeObjectURL(downloadUrl);
                status.textContent = 'Branded photo downloaded.';
            } catch (error) {
                status.textContent = error.message || 'The branded photo could not be prepared.';
            } finally {
                button.disabled = false;
            }
        });
    });
</script>
</body>
</html>
