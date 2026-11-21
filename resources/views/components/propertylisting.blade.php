@props(['property'])

<a href="{{ route('properties.show',  $property)}}" class='text-decoration-none'>
    <div class="card h-100 shadow-sm">
        @if ($property->images->first())
            <img src="{{ $property->images->first()->url }}" alt="{{ $property->name }}"
                class="card-img-top" style="height: 200px; object-fit: cover;">
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
</a>