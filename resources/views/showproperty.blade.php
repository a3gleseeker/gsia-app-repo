<x-layout>
    <x-slot:title>
        {{ $property->name }}
    </x-slot:title>

    @php
        $pricePerSqm = (float) $property->size > 0 ? (float) $property->price / (float) $property->size : null;
        $propertyTypes = ['Residential', 'Commercial', 'Industrial', 'Lot'];
    @endphp

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/maplibre-gl@4.7.1/dist/maplibre-gl.css" rel="stylesheet">
    <link href="{{ asset('css/showprop.css') }}" rel="stylesheet">

    <div class="container py-4 pd">
        {{-- Top bar --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('home') }}" class="pd-back">&larr; Back to listings</a>

            @can('level-1')
                <div class="navbar-brand">
                    <button type="button" class="btn btn-dark btn-sm px-3"
                        data-bs-toggle="modal" data-bs-target="#editPropertyModal">
                    Edit property
                    </button>
                    <!--
                        <button type="button" class="btn btn-dark btn-sm px-3"
                            data-bs-toggle="modal" data-bs-target="#">
                            Unlist Property
                        </button>
                    -->
                </div>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4 g-xl-5">
            {{-- Main column --}}
            <div class="col-12 col-lg-8">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h1 class="pd-title">{{ $property->name }}</h1>
                        <p class="pd-address">{{ $property->city }}, {{ $property->postal }}</p>
                    </div>
                    <span class="pd-status {{ $property->status === 'sold' ? 'is-sold' : 'is-available' }} mt-2">
                        {{ $property->status === 'sold' ? 'Sold' : 'Available' }}
                    </span>
                </div>

                {{-- Gallery --}}
                @if ($property->images->isNotEmpty())
                    <div class="pd-gallery">
                        <div id="propertyCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach ($property->images as $img)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                        <img src="{{ $img->url }}" alt="{{ $property->name }}, photo {{ $loop->iteration }}"
                                             @unless ($loop->first) loading="lazy" @endunless>
                                    </div>
                                @endforeach
                            </div>

                            @if ($property->images->count() > 1)
                                <button class="carousel-control-prev" type="button"
                                        data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous photo</span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                        data-bs-target="#propertyCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next photo</span>
                                </button>
                            @endif
                        </div>

                        <span class="pd-counter" id="galleryCounter">1 / {{ $property->images->count() }}</span>

                        @if ($property->status === 'sold')
                            <div class="pd-sold-veil"><span>Sold</span></div>
                        @endif
                    </div>

                    @if ($property->images->count() > 1)
                        <div class="pd-thumbs" id="galleryThumbs">
                            @foreach ($property->images as $img)
                                <button type="button" class="pd-thumb {{ $loop->first ? 'is-active' : '' }}"
                                        data-bs-target="#propertyCarousel" data-bs-slide-to="{{ $loop->index }}"
                                        aria-label="Show photo {{ $loop->iteration }}">
                                    <img src="{{ $img->url }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="pd-gallery pd-empty">No photos yet</div>
                @endif

                {{-- Key facts --}}
                <dl class="pd-facts">
                    <div class="pd-fact">
                        <dt>Type</dt>
                        <dd>{{ $property->type }}</dd>
                    </div>
                    <div class="pd-fact">
                        <dt>Size</dt>
                        <dd>{{ number_format($property->size, 2) }} sqm</dd>
                    </div>
                    @if ($pricePerSqm)
                        <div class="pd-fact">
                            <dt>Price per sqm</dt>
                            <dd>₱{{ number_format($pricePerSqm, 0) }}</dd>
                        </div>
                    @endif
                    <div class="pd-fact">
                        <dt>City</dt>
                        <dd>{{ $property->city }}</dd>
                    </div>
                </dl>

                {{-- Description --}}
                @if ($property->description)
                    <section class="pd-section">
                        <h2>About this property</h2>
                        <p class="pd-description">{{ $property->description }}</p>
                    </section>
                @endif

                {{-- Location --}}
                @if ($property->latitude && $property->longitude)
                    <section class="pd-section">
                        <h2>Location</h2>
                        <div id="map" class="pd-map"
                             data-lat="{{ $property->latitude }}"
                             data-lng="{{ $property->longitude }}"
                             data-name="{{ $property->name }}"></div>
                        <a class="pd-maplink" target="_blank" rel="noopener"
                           href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}">
                            Open in Google Maps
                        </a>
                    </section>
                @endif
            </div>

            <div class="col-12 col-lg-4">
                <aside class="pd-panel">
                    <div class="pd-price">₱{{ number_format($property->price, 2) }}</div>
                    <span>Viewed by {{$view_count}} people</span>
                    @if ($pricePerSqm)
                        <div class="pd-price-sub">About ₱{{ number_format($pricePerSqm, 0) }} per sqm</div>
                    @endif

                    <hr>

                    <p class="pd-meta">
                        Listed {{ $property->created_at->diffForHumans() }}@if ($property->user) by {{ $property->user->name }}@endif
                    </p>

                    @unless ($property->status === 'sold')
                        <button type="button" class="btn btn-dark pd-inquire"
                                data-bs-toggle="modal" data-bs-target="#inquiryModal">
                            Inquire now
                        </button>
                    @endunless

                </aside>
            </div>
        </div>
    </div>

    {{-- Mobile bar: price + Inquire always in reach on phones --}}
    <div class="pd-mobile-bar d-lg-none">
        <div class="pd-mobile-bar-price">₱{{ number_format($property->price, 2) }}</div>
        @unless ($property->status === 'sold')
            <button type="button" class="btn btn-dark"
                    data-bs-toggle="modal" data-bs-target="#inquiryModal">
                Inquire
            </button>
        @else
            <span class="pd-status is-sold">Sold</span>
        @endunless
    </div>

    @unless ($property->status === 'sold')
        <x-inquiryform :property="$property" />
    @endunless

    @can('level-1')
        {{-- Edit modal --}}
        <div class="modal fade pd-modal" id="editPropertyModal" tabindex="-1"
             aria-labelledby="editPropertyLabel" aria-hidden="true"
             data-reopen="{{ ($errors->any() || session('reopen_modal')) ? '1' : '0' }}">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <form class="modal-content" method="POST"
                      action="{{ route('properties.update', $property) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editPropertyLabel">Edit property</h5>
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

                        <div class="pd-group">
                            <h6>Details</h6>
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
                                    <label for="postal" class="form-label">Postal code</label>
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
                                    <select id="type" name="type" class="form-select" required>
                                        @foreach ($propertyTypes as $option)
                                            <option value="{{ $option }}" @selected(old('type', $property->type) === $option)>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select id="edit_status" name="status"
                                            class="form-select @error('status') is-invalid @enderror" required>
                                        @foreach (['active' => 'Active', 'sold' => 'Sold', 'archived' => 'Archived'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('status', $property->status) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" rows="4"
                                              class="form-control">{{ old('description', $property->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="pd-group">
                            <h6>Photos</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Current photos</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse ($property->images as $img)
                                            <div class="position-relative">
                                                <img src="{{ $img->url }}" alt="" class="rounded"
                                                     style="width: 110px; height: 80px; object-fit: cover;">
                                                <button type="submit" form="delete-image-{{ $img->id }}"
                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1"
                                                        aria-label="Remove photo"
                                                        onclick="return confirm('Remove this photo?')">&times;</button>
                                            </div>
                                        @empty
                                            <span class="text-muted small">No photos yet.</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="images" class="form-label">Add photos</label>
                                    <input type="file" id="images" name="images[]" class="form-control"
                                           accept="image/*" multiple>
                                    <div class="form-text">Maximum 1 MB per photo, up to 10 at a time. You can pick more than once.</div>
                                    <div id="selectedImages" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="pd-group">
                            <h6>Map location</h6>
                            <p class="text-muted small mb-2">Click the map to place the pin.</p>
                            <div id="picker" style="height: 280px;" class="rounded border"></div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $property->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $property->longitude) }}">
                            <button type="button" id="clearPin" class="btn btn-link btn-sm px-0">Remove pin</button>
                        </div>
                    </div>

                    <div class="modal-footer flex-nowrap gap-2">
                        <button type="button" class="btn btn-danger me-auto" id="deleteBtn">
                            Delete<span class="d-none d-sm-inline"> Listing</span>
                        </button>

                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">
                            Save<span class="d-none d-sm-inline"> changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Separate forms (outside the edit form, since forms can't be nested) --}}
        <form id="deleteForm" method="POST" action="{{ route('properties.destroy', $property) }}">
            @csrf
            @method('DELETE')
        </form>

        @foreach ($property->images as $img)
            <form id="delete-image-{{ $img->id }}" method="POST"
                  action="{{ route('properties.images.destroy', [$property, $img]) }}">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endcan

    {{-- MapLibre first, then the page script (public/js/showprop.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
    <script src="{{ asset('js/showprop.js') }}"></script>
</x-layout>