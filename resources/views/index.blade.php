<x-layout :fluid="true">
    <x-slot:title>
        GSIA | Property Listings
    </x-slot:title>

    <style>
        .gsia-hero {
            background:
                linear-gradient(135deg, rgba(13, 42, 84, .95), rgba(13, 110, 253, .85)),
                url('{{ asset('images/hero.jpg') }}') center / cover no-repeat;
            color: #fff;
            padding: clamp(2.5rem, 7vw, 5rem) 0;
        }
        .gsia-hero h1 { font-size: clamp(1.75rem, 5vw, 3rem); letter-spacing: -.02em; }
        .gsia-hero .lead { font-size: clamp(1rem, 2.5vw, 1.2rem); opacity: .9; }
        .gsia-search { max-width: 52rem; }
        .gsia-search .form-control,
        .gsia-search .form-select { border: 0; box-shadow: none; }
        #results { scroll-margin-top: 5rem; }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .gsia-results > .col > a { transition: transform .2s ease; }
            .gsia-results > .col > a:hover { transform: translateY(-4px); }
            .gsia-results > .col > a:hover .card { box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .12) !important; }
        }
    </style>

    {{-- Hero + search --}}
    <section class="gsia-hero text-center">
        <div class="container">
            <h1 class="fw-bold mb-3">Find Your Next Property</h1>
            <p class="lead mx-auto mb-4" style="max-width: 36rem;">
                Browse the latest GSIA listings, handpicked and ready to view.
            </p>

            <form method="GET" action="{{ url()->current() }}#results"
                  class="gsia-search mx-auto bg-white rounded-4 shadow p-2" role="search">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md">
                        <label for="q" class="visually-hidden">Search properties</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 text-secondary">
                                <i class="bi bi-search" aria-hidden="true"></i>
                            </span>
                            <input type="search" id="q" name="q" value="{{ $q }}"
                                   class="form-control" placeholder="Search by name, city or postal code"
                                   enterkeyhint="search" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-12 col-md-4 border-md-start">
                        <label for="type" class="visually-hidden">Property type</label>
                        <select id="type" name="type" class="form-select">
                            <option value="">All types</option>
                            @foreach ($types as $t)
                                <option value="{{ $t }}" @selected($type === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary w-100 px-4 rounded-3">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div class="container py-4 py-md-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($searching)
            {{-- Search results --}}
            <section id="results" aria-live="polite">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
                    <h2 class="h4 fw-bold mb-0">
                        {{ $results->total() }} {{ Str::plural('result', $results->total()) }}
                        @if ($q !== '') for “{{ $q }}” @endif
                        @if ($type) <span class="badge text-bg-light border text-secondary fw-normal ms-1">{{ $type }}</span> @endif
                    </h2>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm align-self-start">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Clear search
                    </a>
                </div>

                @if ($results->isNotEmpty())
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 gsia-results">
                        @foreach ($results as $property)
                            <div class="col">
                                <x-propertylisting :property="$property" />
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        {{ $results->links() }}
                    </div>
                @else
                    <div class="text-center bg-body-tertiary border rounded-4 py-5 px-3">
                        <i class="bi bi-search display-4 text-secondary" aria-hidden="true"></i>
                        <h3 class="h5 mt-3">No matching properties</h3>
                        <p class="text-muted">Try a different keyword, or remove the type filter.</p>
                        <a href="{{ url()->current() }}" class="btn btn-primary">Show all listings</a>
                    </div>
                @endif
            </section>
        @else
            {{-- Default browse sections --}}
            <x-property-section
                title="New Properties"
                subtitle="Recently added listings"
                icon="stars"
                :properties="$newProperties" />

            <x-property-section
                title="You May Also Like"
                subtitle="A random pick from our listings"
                icon="shuffle"
                :properties="$randomProperties" />

            @if ($newProperties->isEmpty() && $randomProperties->isEmpty())
                <div class="text-center bg-body-tertiary border rounded-4 py-5 px-3">
                    <i class="bi bi-house-door display-4 text-secondary" aria-hidden="true"></i>
                    <h3 class="h5 mt-3">No properties yet</h3>
                    <p class="text-muted mb-0">Check back soon, new listings are added regularly.</p>
                </div>
            @endif
        @endif
    </div>
</x-layout>