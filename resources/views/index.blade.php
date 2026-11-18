<x-layout>
    <x-slot:title>
        GSIA
    </x-slot:title>

    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="text-center my-4">
            <h1 class="fw-bold">GSIA Property Listings</h1>
            <p class="text-muted">Browse available properties</p>
        </div>

        <div class="row g-4">
            @forelse ($properties as $property)
                <div class="col-12 col-sm-6 col-lg-4">
                    <x-propertylisting :property="$property" />
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No properties yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layout>