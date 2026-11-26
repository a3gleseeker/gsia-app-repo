@props(['title', 'properties', 'subtitle' => null, 'href' => null, 'icon' => null])

@once
    <style>
        .gsia-rail {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-padding-inline: .75rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            margin-inline: -.75rem;
            padding: .25rem .75rem 1rem;
        }
        .gsia-rail::-webkit-scrollbar { display: none; }
        .gsia-rail > * {
            flex: 0 0 82%;
            max-width: 20rem;
            scroll-snap-align: start;
        }
        .gsia-rail > * > * { height: 100%; width: 100%; }

        @media (min-width: 576px) {
            .gsia-rail > * { flex-basis: 46%; }
        }
        @media (min-width: 768px) {
            .gsia-rail {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                overflow: visible;
                margin-inline: 0;
                padding: 0;
            }
            .gsia-rail > * { max-width: none; }
        }
        @media (min-width: 992px) {
            .gsia-rail { grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        }

        .gsia-section-title::after {
            content: "";
            display: block;
            width: 56px;
            height: 4px;
            margin-top: .5rem;
            border-radius: 2px;
            background: var(--bs-primary);
        }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .gsia-rail > * > a { transition: transform .2s ease; }
            .gsia-rail > * > a:hover { transform: translateY(-4px); }
            .gsia-rail > * > a:hover .card { box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .12) !important; }
        }
    </style>
@endonce

@if ($properties->isNotEmpty())
    <section class="mb-4 mb-md-5" aria-labelledby="sec-{{ Str::slug($title) }}">
        <div class="d-flex justify-content-between align-items-end gap-3 mb-3">
            <div>
                <h2 id="sec-{{ Str::slug($title) }}" class="gsia-section-title h4 fw-bold mb-0">
                    @if ($icon)<i class="bi bi-{{ $icon }} text-primary me-2" aria-hidden="true"></i>@endif{{ $title }}
                </h2>
                @if ($subtitle)
                    <p class="text-muted small mt-2 mb-0">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($href)
                <a href="{{ $href }}" class="small fw-semibold text-nowrap text-decoration-none">
                    View all <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            @endif
        </div>

        <div class="gsia-rail">
            @foreach ($properties as $property)
                <div><x-propertylisting :property="$property" /></div>
            @endforeach
        </div>

        
    </section>
@endif