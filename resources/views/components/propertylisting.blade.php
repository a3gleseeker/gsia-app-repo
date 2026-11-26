@props(['property'])

@php
    $image    = $property->images->first();
    $location = collect([$property->city, $property->postal])->filter()->implode(', ');
@endphp

<a href="{{ route('properties.show', $property) }}"
   class="d-block h-100 text-decoration-none text-reset"
   aria-label="View {{ $property->name }}">

    <article class="card h-100 border-0 shadow-sm overflow-hidden rounded-4">

        {{-- Image / placeholder --}}
        <div class="position-relative ratio ratio-4x3 bg-body-secondary">
            @if ($image)
                <img src="{{ $image->url }}"
                     alt="{{ $property->name }}"
                     loading="lazy"
                     class="w-100 h-100 object-fit-cover {{ $property->is_sold ? 'opacity-75' : '' }}"
                     @if ($property->is_sold) style="filter: grayscale(.6);" @endif>
            @else
                <div class="d-flex align-items-center justify-content-center text-secondary">
                    <i class="bi bi-image fs-1" aria-hidden="true"></i>
                </div>
            @endif

            <span class="badge position-absolute top-0 start-0 m-3 shadow-sm {{ $property->is_sold ? 'text-bg-danger' : 'text-bg-success' }}"
                  style="width: fit-content; height: fit-content;">
                {{ $property->is_sold ? 'Sold' : 'Available' }}
            </span>
        </div>

        <div class="card-body d-flex flex-column p-3 p-sm-4">
            <p class="fs-4 fw-bold text-primary mb-1">
                ₱{{ number_format($property->price, 2) }}
            </p>

            <h2 class="h6 card-title fw-semibold mb-1 text-truncate" title="{{ $property->name }}">
                {{ $property->name }}
            </h2>

            @if ($location)
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt-fill me-1" aria-hidden="true"></i>{{ $location }}
                </p>
            @endif
        </div>

        <div class="card-footer bg-transparent border-top-0 pt-0 px-3 px-sm-4 pb-3 pb-sm-4">
            <hr class="mt-0 mb-3 opacity-10">
            <div class="d-flex justify-content-between align-items-center small text-muted">
                <span class="badge text-bg-light border text-secondary fw-medium">
                    {{ $property->type }}
                </span>
                <span>
                    <i class="bi bi-arrows-fullscreen me-1" aria-hidden="true"></i>{{ number_format($property->size, 2) }} sqm
                </span>
            </div>
        </div>
    </article>
</a>