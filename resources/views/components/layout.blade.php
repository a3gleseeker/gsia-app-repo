<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    @vite(['resources/js/app.js'])
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
            <div class="navbar-brand">
                <a class="navbar-brand" href="/">Home</a>
                @auth
                    <a class='navbar-brand' href='/list_property'>List</a>
                    <a class='navbar-brand' href='/#'>Panel</a>
                @endauth

                @auth
                    @can('level-2')
                        <a class='navbar-brand' href='/register'>New Employee</a>
                    @endcan
                @else
                @endauth
            </div>
            <div class="navbar-brand">
                @auth
                    <form method="POST" action="/logout">
                        @csrf
                        <span class="navbar-text me-2">Hello, {{ auth()->user()->name }}</span>
                        <span class="navbar-text me-2"> {{ auth()->user()->level }}</span>
                        
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                @else
                    <a class="navbar-brand" href="/login">Login</a>
                @endauth
            </div>
        </nav>

        <div class="container">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>