
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HM CARS | Vehicle Photos</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            color: #1b2638;
        }

        header {
            background: #10233f;
            color: white;
            padding: 24px;
        }

        header h1 { margin: 0 0 8px; }

        header p {
            margin: 0;
            color: #d8e2f0;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,.06);
        }

        .button {
            display: inline-block;
            padding: 11px 17px;
            border: 0;
            border-radius: 6px;
            background: #10233f;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .button:hover { background: #1c3b65; }

        .button.secondary {
            background: #e2e8f0;
            color: #1b2638;
        }

        .button:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .upload-area {
            border: 2px dashed #94a3b8;
            border-radius: 10px;
            padding: 25px;
            background: #f8fafc;
            text-align: center;
        }

        input[type=file] {
            max-width: 100%;
            margin: 15px 0;
        }

        .hint {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .photo {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .photo img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .photo-info {
            padding: 10px;
            font-size: 13px;
        }

        .primary {
            color: #166534;
            font-weight: bold;
        }

        .success {
            padding: 14px;
            background: #dcfce7;
            color: #166534;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .errors {
            padding: 14px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        @media(max-width:600px) {
            .card { padding: 16px; }
        }
        
.button-delete {
    background: #dc2626;
    color: white;
    font-size: 12px;
    padding: 9px 12px;
    width: 100%;
}

.button-delete:hover {
    background: #b91c1c;
}

.button-delete:disabled {
    background: #cbd5e1;
    color: #64748b;
    cursor: not-allowed;
}

        .photo-delete-form,
        .photo-replace-form { padding: 0 10px 10px; }
        .photo-replace-form input { max-width: 100%; margin: 0 0 10px; }
        .button-main { width: 100%; }
    </style>
</head>

<body>

<header>
    <h1>HM CARS — Vehicle Photos</h1>
    <p>The Right Car. The Right Connection.</p>
</header>

<main class="container">

    <div class="top-bar">
        <div>
            <h2>{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</h2>
            <p>Manage vehicle photos — ID #{{ $vehicle->id }}</p>
        </div>

        <a href="{{ route('admin.vehicles.index') }}"
           class="button secondary">
            Back to Vehicles
        </a>
    </div>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif
    
@if(session('error'))
    <div class="errors">
        {{ session('error') }}
    </div>
@endif


    @if($errors->any())
        <div class="errors">
            <strong>Please correct these errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h3>Upload Vehicle Photos</h3>

        <p class="hint">
            Upload 5–10 photos for a new gallery.
            You can add more photos later, up to a maximum of 10.
            Each photo must be JPG, PNG or WebP and no larger than 5 MB.
        </p>

        <p>
            <strong>Uploaded:</strong>
            {{ $vehicle->images->count() }} / 10 photos
        </p>

        @if($vehicle->images->count() < 10)
            <form method="POST"
                  action="{{ route('admin.vehicles.images.store', $vehicle) }}"
                  enctype="multipart/form-data"
                  id="photoForm">
                @csrf

                <div class="upload-area">
                    <label for="photos">
                        <strong>Select vehicle photos</strong>
                    </label>

                    <div>
                        <input type="file"
                               name="photos[]"
                               id="photos"
                               accept=".jpg,.jpeg,.png,.webp"
                               multiple
                               required>
                    </div>

                    <p class="hint" id="photoCount">
                        No photos selected
                    </p>
                </div>

                <div id="previewGallery" class="gallery"></div>

                <div style="margin-top:20px;">
                    <button type="submit"
                            class="button"
                            id="uploadButton">
                        Upload Photos
                    </button>
                </div>
            </form>
        @else
            <p class="hint">
                This vehicle already has the maximum of 10 photos.
            </p>
        @endif
    </div>

    <div class="card">
        <h3>Existing Vehicle Photos</h3>

        @if($vehicle->images->isEmpty())
            <p class="hint">
                No photos have been uploaded for this vehicle yet.
            </p>
        @else
            <div class="gallery">
                @foreach($vehicle->images as $image)
                    <div class="photo">
                        <img
                            src="{{ asset('storage/' . $image->image_path) }}"
                            alt="Photo of {{ $vehicle->make }} {{ $vehicle->model }}"
                            loading="lazy"
                        >

                        <div class="photo-info">
                            @if($image->is_primary)
                                <span class="primary">★ Main Photo</span>
                            @else
                                <form
                                    method="POST"
                                    action="{{ route('admin.vehicles.images.primary', ['vehicle' => $vehicle->id, 'image' => $image->id]) }}"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="button button-main">
                                        Set as Main
                                    </button>
                                </form>
                            @endif
                        </div>

                        <form
                            class="photo-replace-form"
                            method="POST"
                            action="{{ route('admin.vehicles.images.replace', ['vehicle' => $vehicle->id, 'image' => $image->id]) }}"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <label for="replacement-photo-{{ $image->id }}">Replace photo</label>
                            <input
                                type="file"
                                id="replacement-photo-{{ $image->id }}"
                                name="photo"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                            >
                            <button type="submit" class="button button-main">
                                Replace Photo
                            </button>
                        </form>

                        <form
                            class="photo-delete-form"
                            method="POST"
                            action="{{ route('admin.vehicles.images.destroy', ['vehicle' => $vehicle->id, 'image' => $image->id]) }}"
                            onsubmit="return confirm('Are you sure you want to delete this photo?');"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="button button-delete"
                                @disabled($vehicle->images->count() <= 5)
                            >
                                Delete Photo
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</main>

<script>
    const input = document.getElementById('photos');
    const countLabel = document.getElementById('photoCount');
    const previewGallery = document.getElementById('previewGallery');
    const uploadButton = document.getElementById('uploadButton');

    const existingCount = Number('{{ $vehicle->images->count() }}');

    if (input) {
        input.addEventListener('change', function () {
            previewGallery.innerHTML = '';

            const files = Array.from(input.files);
            const total = existingCount + files.length;

            countLabel.textContent =
                files.length + ' selected — ' +
                total + ' / 10 total photos';

            const validCount = existingCount === 0
                ? files.length >= 5 && files.length <= 10
                : files.length >= 1 && total <= 10;

            uploadButton.disabled = !validCount;

            if (!validCount) {
                countLabel.textContent +=
                    existingCount === 0
                        ? ' (select 5–10 photos)'
                        : ' (maximum 10 photos total)';
            }

            files.forEach(file => {
                const previewUrl = URL.createObjectURL(file);

                const wrapper = document.createElement('div');
                wrapper.className = 'photo';

                const image = document.createElement('img');
                image.src = previewUrl;
                image.alt = file.name;

                image.addEventListener('load', () => {
                    URL.revokeObjectURL(previewUrl);
                }, { once: true });

                const info = document.createElement('div');
                info.className = 'photo-info';
                info.textContent = file.name;

                wrapper.appendChild(image);
                wrapper.appendChild(info);
                previewGallery.appendChild(wrapper);
            });
        });

        // Require a valid selection before submitting.
        uploadButton.disabled = true;
    }
</script>

</body>
</html>
