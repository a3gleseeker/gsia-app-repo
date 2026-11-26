@props(['fluid' => false])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'GSIA' }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .gsia-nav { background: #0d2a54; }
        .gsia-nav .nav-link { color: rgba(255, 255, 255, .8); }
        .gsia-nav .nav-link:hover,
        .gsia-nav .nav-link.active { color: #fff; }
        .gsia-nav .nav-link.active { box-shadow: inset 0 -2px 0 var(--bs-primary); }
        @media (max-width: 991.98px) {
            .gsia-nav .nav-link.active { box-shadow: none; background: rgba(255, 255, 255, .1); border-radius: .375rem; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary">

    <nav class="navbar navbar-expand-lg navbar-dark gsia-nav sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-buildings me-1" aria-hidden="true"></i> GSIA
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNav" aria-controls="mainNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Submit Documents</a>
                    </li>

                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('list_property*') ? 'active' : '' }}" href="/list_property">List</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('adminindex*') ? 'active' : '' }}" href="/admin">Panel</a>
                        </li>
                        @can('level-2')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('register*') ? 'active' : '' }}" href="/register">New Employee</a>
                            </li>
                        @endcan
                    @endauth
                </ul>

                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2 w-100 w-lg-auto"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="badge text-bg-primary">{{ auth()->user()->level }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="/logout">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a class="btn btn-primary btn-sm px-3" href="/login">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if ($fluid)
            {{ $slot }}
        @else
            <div class="container py-4">
                {{ $slot }}
            </div>
        @endif
    </main>

    <footer class="bg-dark text-white-50 py-4 mt-5">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small">
            <span>&copy; {{ date('Y') }} GSIA. All rights reserved.</span>
            <span>Property listings &amp; document services</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>