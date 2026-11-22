<x-layout>
    <x-slot:title>
        Post Property
    </x-slot:title>

    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet">

    <style>
        .post-form { padding-bottom: calc(5.5rem + env(safe-area-inset-bottom, 0px)); }

        .action-bar {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            z-index: 1030;
            display: flex;
            gap: .5rem;
            padding: .75rem 1rem calc(.75rem + env(safe-area-inset-bottom, 0px));
            background: var(--bs-body-bg, #fff);
            border-top: 1px solid var(--bs-border-color, #dee2e6);
        }
        .action-bar .btn { min-height: 48px; }
        .action-bar .btn-primary { flex: 1; }

        /* Photos */
        #dropzone {
            border-style: dashed !important;
            cursor: pointer;
            min-height: 120px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        #previews {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
            gap: .5rem;
        }
        .photo-remove {
            position: absolute; top: .25rem; right: .25rem;
            width: 32px; height: 32px;
            padding: 0; line-height: 1;
            border-radius: 50%;
            font-size: 1.25rem;
        }

        /* Map picker */
        #picker { height: 300px; }
        @media (min-width: 992px) { #picker { height: 340px; } }

        @media (min-width: 992px) {
            .post-form { padding-bottom: 0; }
            .action-bar {
                position: static;
                justify-content: flex-end;
                padding: 0;
                margin-top: 1.5rem;
                background: transparent;
                border-top: 0;
            }
            .action-bar .btn-primary { flex: 0 0 auto; padding-inline: 1.5rem; }
            .photos-card { position: sticky; top: 1rem; }
        }
    </style>

    <div class="container py-3 py-lg-4">
        <a href="{{ route('home') }}" class="btn btn-link px-0 mb-1 text-decoration-none">&larr; Back to listings</a>

        <div class="mb-3 mb-lg-4">
            <h1 class="h4 h3-lg mb-1">Post a new property</h1>
            <p class="text-muted small mb-0">Fill in the details and add photos. The first photo is the cover.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/properties" enctype="multipart/form-data" class="post-form">
            @csrf

            <div class="row g-3 g-lg-4">
                {{-- Details --}}
                <div class="col-12 col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white fw-semibold">Property details</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" autocomplete="off" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select id="type" name="type"
                                            class="form-select form-select-lg @error('type') is-invalid @enderror"
                                            required>
                                        <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select a type</option>
                                        @foreach (['Residential', 'Commercial', 'Industrial', 'Lot'] as $option)
                                            <option value="{{ $option }}" {{ old('type') === $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-sm-6">
                                    <label for="size" class="form-label">Size</label>
                                    <div class="input-group input-group-lg has-validation">
                                        <input type="number" step="0.01" min="0" inputmode="decimal" id="size" name="size"
                                               class="form-control @error('size') is-invalid @enderror"
                                               value="{{ old('size') }}" required>
                                        <span class="input-group-text">sqm</span>
                                        @error('size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" step="0.01" min="0" inputmode="decimal" id="price" name="price"
                                               class="form-control @error('price') is-invalid @enderror"
                                               value="{{ old('price') }}" required>
                                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-8">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" id="city" name="city" autocomplete="address-level2"
                                           class="form-control form-control-lg @error('city') is-invalid @enderror"
                                           value="{{ old('city') }}" required>
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 col-sm-4">
                                    <label for="postal" class="form-label">Postal code</label>
                                    <input type="text" id="postal" name="postal" inputmode="numeric" autocomplete="postal-code"
                                           class="form-control form-control-lg @error('postal') is-invalid @enderror"
                                           value="{{ old('postal') }}" required>
                                    @error('postal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">
                                        Description <span class="text-muted small">(optional)</span>
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="form-control form-control-lg @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Map location --}}
                                <div class="col-12">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                        <label class="form-label mb-0">
                                            Map location <span class="text-muted small">(optional)</span>
                                        </label>
                                        <div class="btn-group btn-group-sm" id="pickerStyles" role="group" aria-label="Map style">
                                            <button type="button" class="btn btn-outline-secondary active" data-style="colorful">Colorful</button>
                                            <button type="button" class="btn btn-outline-secondary" data-style="satellite">Satellite</button>
                                            <button type="button" class="btn btn-outline-secondary" data-style="standard">Standard</button>
                                        </div>
                                    </div>

                                    <div id="picker" class="rounded border"></div>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="form-text" id="pinStatus">Tap or click the map to place the pin.</span>
                                        <button type="button" id="clearPin" class="btn btn-link btn-sm px-0 d-none">Remove pin</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Photos --}}
                <div class="col-12 col-lg-5">
                    <div class="card shadow-sm photos-card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Photos</span>
                            <span class="badge text-bg-secondary" id="imageCount">0 / 10</span>
                        </div>
                        <div class="card-body">
                            <label for="images" id="dropzone" class="text-center border border-2 rounded p-3 mb-3">
                                <span class="fs-2 lh-1 mb-2">&#128247;</span>
                                <span class="fw-semibold">
                                    <span class="d-lg-none">Tap to add photos</span>
                                    <span class="d-none d-lg-inline">Click to choose or drag photos here</span>
                                </span>
                                <span class="form-text">JPG, PNG or WebP · up to 10 · large photos are shrunk to fit 1 MB</span>
                                <input type="file" id="images" name="images[]" class="d-none"
                                       accept="image/*" multiple>
                            </label>

                            <div id="photoNotice" class="alert alert-warning small py-2 d-none" role="status"></div>

                            <div id="previews"></div>

                            <p id="emptyHint" class="text-muted small text-center mb-0">No photos added yet.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary d-flex align-items-center">Cancel</a>
                <button type="submit" class="btn btn-primary">Post property</button>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    {{-- Photos --}}
    <script>
        const input = document.getElementById('images');
        const dropzone = document.getElementById('dropzone');
        const grid = document.getElementById('previews');
        const counter = document.getElementById('imageCount');
        const emptyHint = document.getElementById('emptyHint');
        const notice = document.getElementById('photoNotice');
        const selected = new DataTransfer();
        const MAX_FILES = 10;
        const MAX_BYTES = 1024 * 1024;
        const MAX_DIMENSION = 1600;
        let busy = false;

        // Phone photos are usually 2–6 MB, so shrink them in the browser instead of rejecting them.
        async function shrink(file) {
            if (file.size <= MAX_BYTES) return file;
            try {
                const bitmap = await createImageBitmap(file);
                const scale = Math.min(1, MAX_DIMENSION / Math.max(bitmap.width, bitmap.height));
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(bitmap.width * scale);
                canvas.height = Math.round(bitmap.height * scale);
                canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                for (let q = 0.85; q >= 0.4; q -= 0.15) {
                    const blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', q));
                    if (blob && blob.size <= MAX_BYTES) {
                        return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg',
                            { type: 'image/jpeg', lastModified: file.lastModified });
                    }
                }
            } catch (e) { /* fall through */ }
            return null;
        }

        function showNotice(lines) {
            notice.textContent = lines.join(' ');
            notice.classList.toggle('d-none', lines.length === 0);
        }

        async function addFiles(fileList) {
            if (busy) return;
            busy = true;
            const files = Array.from(fileList); // copy first: input.files gets replaced below
            const messages = [];
            const skipped = [];

            for (const original of files) {
                if (!original.type.startsWith('image/')) continue;
                if (selected.files.length >= MAX_FILES) {
                    messages.push('Maximum of ' + MAX_FILES + ' photos.');
                    break;
                }

                const f = await shrink(original);
                if (!f) { skipped.push(original.name); continue; }

                const duplicate = Array.from(selected.files).some(x =>
                    x.name === f.name && x.lastModified === f.lastModified);
                if (!duplicate) selected.items.add(f);
            }

            if (skipped.length) messages.push('Could not use: ' + skipped.join(', ') + '.');
            showNotice(messages);

            input.files = selected.files;
            render();
            busy = false;
        }

        input.addEventListener('change', () => addFiles(input.files));

        ['dragenter', 'dragover'].forEach(ev =>
            dropzone.addEventListener(ev, e => {
                e.preventDefault();
                dropzone.classList.add('border-primary', 'bg-light');
            }));

        ['dragleave', 'drop'].forEach(ev =>
            dropzone.addEventListener(ev, e => {
                e.preventDefault();
                dropzone.classList.remove('border-primary', 'bg-light');
            }));

        dropzone.addEventListener('drop', e => addFiles(e.dataTransfer.files));

        function removeAt(index) {
            const keep = Array.from(selected.files).filter((_, i) => i !== index);
            selected.items.clear();
            keep.forEach(f => selected.items.add(f));
            input.files = selected.files;
            showNotice([]);
            render();
        }

        function render() {
            grid.innerHTML = '';
            counter.textContent = selected.files.length + ' / ' + MAX_FILES;
            emptyHint.classList.toggle('d-none', selected.files.length > 0);

            Array.from(selected.files).forEach((file, i) => {
                const tile = document.createElement('div');
                tile.className = 'position-relative ratio ratio-4x3';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Photo ' + (i + 1);
                img.className = 'rounded w-100 h-100';
                img.style.objectFit = 'cover';
                img.onload = () => URL.revokeObjectURL(img.src);
                tile.appendChild(img);

                if (i === 0) {
                    const badge = document.createElement('span');
                    badge.className = 'badge text-bg-primary position-absolute bottom-0 start-0 m-1';
                    badge.style.width = 'fit-content';
                    badge.style.height = 'fit-content';
                    badge.textContent = 'Cover';
                    tile.appendChild(badge);
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-danger photo-remove';
                btn.setAttribute('aria-label', 'Remove photo ' + (i + 1));
                btn.innerHTML = '&times;';
                btn.addEventListener('click', () => removeAt(i));
                tile.appendChild(btn);

                grid.appendChild(tile);
            });
        }
    </script>

    {{-- Map pin picker --}}
    <script>
        (function () {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const status = document.getElementById('pinStatus');
            const clearBtn = document.getElementById('clearPin');

            const raster = (tiles, attribution) => ({
                version: 8,
                sources: { base: { type: 'raster', tiles: [tiles], tileSize: 256, attribution: attribution } },
                layers: [{ id: 'base', type: 'raster', source: 'base' }]
            });

            const styles = {
                colorful:  'https://tiles.openfreemap.org/styles/bright',
                standard:  raster('https://tile.openstreetmap.org/{z}/{x}/{y}.png', '&copy; OpenStreetMap contributors'),
                satellite: raster('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', 'Imagery &copy; Esri')
            };

            // Restores the pin if validation failed and the page reloaded with old values.
            const hasPoint = latInput.value !== '' && lngInput.value !== '';
            const start = hasPoint
                ? [parseFloat(lngInput.value), parseFloat(latInput.value)]
                : [123.8854, 10.3157]; // default center [lng, lat]: change to where your properties usually are

            const picker = new maplibregl.Map({
                container: 'picker',
                style: styles.colorful,
                center: start,
                zoom: hasPoint ? 16 : 11,
                cooperativeGestures: true // one finger scrolls the page, two fingers move the map
            });
            picker.addControl(new maplibregl.NavigationControl());
            picker.addControl(new maplibregl.FullscreenControl());

            let pin = null;

            function setPin(lngLat) {
                if (pin) pin.setLngLat(lngLat);
                else pin = new maplibregl.Marker({ color: '#0d6efd' }).setLngLat(lngLat).addTo(picker);
                latInput.value = lngLat.lat.toFixed(7);
                lngInput.value = lngLat.lng.toFixed(7);
                status.textContent = 'Pin placed. Click elsewhere to move it.';
                clearBtn.classList.remove('d-none');
            }

            if (hasPoint) setPin(new maplibregl.LngLat(start[0], start[1]));

            picker.on('click', e => setPin(e.lngLat));

            clearBtn.addEventListener('click', () => {
                if (pin) { pin.remove(); pin = null; }
                latInput.value = '';
                lngInput.value = '';
                status.textContent = 'Tap or click the map to place the pin.';
                clearBtn.classList.add('d-none');
            });

            document.querySelectorAll('#pickerStyles button').forEach(btn => {
                btn.addEventListener('click', () => {
                    picker.setStyle(styles[btn.dataset.style]);
                    document.querySelectorAll('#pickerStyles button')
                        .forEach(b => b.classList.toggle('active', b === btn));
                });
            });
        })();
    </script>
</x-layout>