<x-layout>
    <x-slot:title>
        {{ $property->name }}
    </x-slot:title>

    <div class="container py-4">
        <div class="navbar">
            <div class="navbar-brand">
                <a href="{{ route('home') }}" class="btn btn-link px-0 mb-3 text-decoration-none">&larr; Back to listings</a>
            </div>

            <div class="navbar-brand">
                <a href='#' class='btn btn-warning'>Edit</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                @if ($property->link)
                    <img src="{{ $property->link }}"
                         alt="{{ $property->name }}"
                         class="img-fluid rounded shadow-sm w-100"
                         style="max-height: 450px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                         style="height: 300px;">
                        No image
                    </div>
                @endif
            </div>

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
            </div>
        </div>
    </div>
</x-layout>