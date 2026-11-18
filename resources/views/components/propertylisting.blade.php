@props(['property'])

<div class="card h-100 shadow-sm">
    @if ($property->link)
        <img src="{{ $property->link }}"
             class="card-img-top"
             alt="{{ $property->name }}"
             style="height: 180px; object-fit: cover;">
    @else
        <div class="bg-light d-flex align-items-center justify-content-center text-muted"
             style="height: 180px;">
            No image
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title mb-0">{{ $property->name }}</h5>
            <span class="badge {{ $property->is_sold ? 'bg-danger' : 'bg-success' }}">
                {{ $property->is_sold ? 'Sold' : 'Available' }}
            </span>
        </div>

        <p class="text-muted small mb-2">
            {{ $property->city }}, {{ $property->postal }}
        </p>

        <p class="fs-5 fw-bold text-primary mb-2">
            ₱{{ number_format($property->price, 2) }}
        </p>

        <div class="small">
            <span class="badge bg-secondary">{{ $property->type }}</span>
            <span class="ms-2">{{ number_format($property->size, 2) }} sqm</span>
        </div>
    </div>
</div>