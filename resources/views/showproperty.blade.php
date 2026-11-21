<x-layout>
    <x-slot:title>
        {{ $property->name }}
    </x-slot:title>

    <div class="container py-4">
        <div class="navbar">
            <div class="navbar-brand">
                <a href="{{ route('home') }}" class="btn btn-link px-0 mb-3 text-decoration-none">&larr; Back to listings</a>
            </div>

            @can('level-1')
                <div class="navbar-brand">
                    <button type="button" class="btn btn-warning"
                            data-bs-toggle="modal" data-bs-target="#editPropertyModal">
                        Edit
                    </button>
                </div>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Images --}}
            <div class="col-12 col-lg-7">
                @if ($property->images->isNotEmpty())
                    <div id="propertyCarousel" class="carousel slide shadow-sm rounded overflow-hidden" data-bs-ride="false">
                        <div class="carousel-inner">
                            @foreach ($property->images as $img)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ $img->url }}" alt="{{ $property->name }}"
                                         class="d-block w-100"
                                         style="max-height: 450px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>

                        @if ($property->images->count() > 1)
                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#propertyCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                         style="height: 300px;">
                        No image
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="col-12 col-lg-5">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h1 class="h3 mb-0">{{ $property->name }}</h1>
                    <span class="badge {{ $property->is_sold ? 'bg-danger' : 'bg-success' }}">
                        {{ $property->is_sold ? 'Sold' : 'Available' }}
                    </span>
                </div>

                <p class="text-muted">{{ $property->city }}, {{ $property->postal }}</p>

                <p class="fs-3 fw-bold text-primary">₱{{ number_format($property->price, 2) }}</p>

                <ul class="list-unstyled">
                    <li><strong>Type:</strong> {{ $property->type }}</li>
                    <li><strong>Size:</strong> {{ number_format($property->size, 2) }} sqm</li>
                    <li class="text-muted small mt-2">
                        Listed {{ $property->created_at->diffForHumans() }}
                        @if ($property->user)
                            by {{ $property->user->name }}
                        @endif
                    </li>
                </ul>

                @if ($property->description)
                    <h2 class="h5 mt-4">Description</h2>
                    <p style="white-space: pre-line;">{{ $property->description }}</p>
                @endif

                @if ($property->latitude && $property->longitude)
                    <h2 class="h5 mt-4">Location</h2>
                    <div id="map" style="height: 300px;" class="rounded border"></div>
                    <a class="small" target="_blank" rel="noopener"
                        href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}">
                        Open in Google Maps
                    </a>
                @endif

                

            </div>
        </div>
    </div>

    @can('level-1')
        {{-- Edit modal --}}
        <div class="modal fade" id="editPropertyModal" tabindex="-1" aria-labelledby="editPropertyLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST"
                      action="{{ route('properties.update', $property) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editPropertyLabel">Edit Property</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                       value="{{ old('name', $property->name) }}" required>
                            </div>

                            <div class="col-md-8">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-control"
                                       value="{{ old('city', $property->city) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="postal" class="form-label">Postal Code</label>
                                <input type="text" id="postal" name="postal" class="form-control"
                                       value="{{ old('postal', $property->postal) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="price" class="form-label">Price (₱)</label>
                                <input type="number" step="0.01" min="0" id="price" name="price" class="form-control"
                                       value="{{ old('price', $property->price) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="size" class="form-label">Size (sqm)</label>
                                <input type="number" step="0.01" min="0" id="size" name="size" class="form-control"
                                       value="{{ old('size', $property->size) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" id="type" name="type" class="form-control"
                                       value="{{ old('type', $property->type) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label for="is_sold" class="form-label">Status</label>
                                <select id="is_sold" name="is_sold" class="form-select">
                                    <option value="0" @selected(! old('is_sold', $property->is_sold))>Available</option>
                                    <option value="1" @selected(old('is_sold', $property->is_sold))>Sold</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Current images</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse ($property->images as $img)
                                        <div class="position-relative">
                                            <img src="{{ $img->url }}" alt="" class="rounded"
                                                 style="width: 110px; height: 80px; object-fit: cover;">
                                            <button type="submit" form="delete-image-{{ $img->id }}"
                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1"
                                                    onclick="return confirm('Remove this image?')">&times;</button>
                                        </div>
                                    @empty
                                        <span class="text-muted small">No images yet.</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="images" class="form-label">Add images</label>
                                <input type="file" id="images" name="images[]" class="form-control"
                                       accept="image/*" multiple>
                                <div class="form-text">Maximum 1 MB per image, up to 10 at a time. You can pick more than once.</div>
                                <div id="selectedImages" class="mt-2"></div>
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea id="description" name="description" rows="4"
                                          class="form-control">{{ old('description', $property->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    Map location <span class="text-muted small">(click the map to place the pin)</span>
                                </label>
                                <div id="picker" style="height: 280px;" class="rounded border"></div>
                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">
                                <button type="button" id="clearPin" class="btn btn-link btn-sm px-0">Remove pin</button>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hidden delete forms: must stay OUTSIDE the edit form --}}
        @foreach ($property->images as $img)
            <form id="delete-image-{{ $img->id }}" method="POST"
                  action="{{ route('properties.images.destroy', [$property, $img]) }}">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        {{-- Reopen the modal after a validation error or an image removal --}}
        @if ($errors->any() || session('reopen_modal'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    new bootstrap.Modal(document.getElementById('editPropertyModal')).show();
                });
            </script>
        @endif

        {{-- Multi-pick image selector with 1 MB check --}}
        <script>
            const imagesInput = document.getElementById('images');
            const selected = new DataTransfer();
            const MAX_FILES = 10;
            const MAX_BYTES = 1024 * 1024;

            imagesInput.addEventListener('change', function () {
                const tooBig = [];

                for (const f of this.files) {
                    if (f.size > MAX_BYTES) { tooBig.push(f.name); continue; }
                    if (selected.files.length >= MAX_FILES) { alert('Maximum of ' + MAX_FILES + ' images at a time.'); break; }

                    const duplicate = Array.from(selected.files).some(x =>
                        x.name === f.name && x.size === f.size && x.lastModified === f.lastModified);
                    if (!duplicate) selected.items.add(f);
                }

                if (tooBig.length) alert('Skipped (over 1 MB):\n' + tooBig.join('\n'));

                this.files = selected.files;
                renderSelected();
            });

            function renderSelected() {
                const list = document.getElementById('selectedImages');
                list.innerHTML = '';

                Array.from(selected.files).forEach((f, i) => {
                    const row = document.createElement('div');
                    row.className = 'd-flex justify-content-between align-items-center small border rounded px-2 py-1 mb-1';
                    row.innerHTML = '<span></span><button type="button" class="btn-close btn-sm" aria-label="Remove"></button>';
                    row.querySelector('span').textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
                    row.querySelector('button').addEventListener('click', () => {
                        const next = new DataTransfer();
                        Array.from(selected.files).forEach((x, j) => { if (j !== i) next.items.add(x); });
                        selected.items.clear();
                        Array.from(next.files).forEach(x => selected.items.add(x));
                        imagesInput.files = selected.files;
                        renderSelected();
                    });
                    list.appendChild(row);
                });
            }
        </script>

        <script>
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            let picker = null, pin = null;

            document.getElementById('editPropertyModal').addEventListener('shown.bs.modal', function () {
                if (picker) { picker.resize(); return; }   // already built, just fix the size

                const hasPoint = latInput.value !== '' && lngInput.value !== '';
                const start = hasPoint
                    ? [parseFloat(lngInput.value), parseFloat(latInput.value)]
                    : [123.8854, 10.3157]; // default center [lng, lat]: change to where your properties usually are

                picker = new maplibregl.Map({
                    container: 'picker',
                    style: 'https://tiles.openfreemap.org/styles/liberty',
                    center: start,
                    zoom: hasPoint ? 15 : 11
                });
                picker.addControl(new maplibregl.NavigationControl());
                if (hasPoint) pin = new maplibregl.Marker().setLngLat(start).addTo(picker);

                picker.on('click', function (e) {
                    if (pin) pin.setLngLat(e.lngLat);
                    else pin = new maplibregl.Marker().setLngLat(e.lngLat).addTo(picker);
                    latInput.value = e.lngLat.lat.toFixed(7);
                    lngInput.value = e.lngLat.lng.toFixed(7);
                });
            });

            document.getElementById('clearPin').addEventListener('click', function () {
                if (pin) { pin.remove(); pin = null; }
                latInput.value = '';
                lngInput.value = '';
            });
        </script>
    @endcan

    <link href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet">
    <script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>

    @if ($property->latitude && $property->longitude)
        <script>
            const pos = [@json($property->longitude), @json($property->latitude)]; // [lng, lat]
            const map = new maplibregl.Map({
                container: 'map',
                style: 'https://tiles.openfreemap.org/styles/liberty',
                center: pos,
                zoom: 15,
                scrollZoom: false // so scrolling the page doesn't get stuck on the map
            });
            map.addControl(new maplibregl.NavigationControl());
            new maplibregl.Marker().setLngLat(pos).addTo(map);
        </script>
    @endif
</x-layout>