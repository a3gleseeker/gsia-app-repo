<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>GSIA</title>
</head>
<body>
    <div class="container">
        <nav class='navbar navbar-light bg-light'>
            <div class="navbar-brand">
                <a class='navbar-brand' href='/'>Home</a>
            </div>
            <div class="navbar-brand">
                @auth
                    <form method='POST' action='/logout'>
                        @csrf
                        <button type='submit' class='navbar-brand btn btn-danger' href="/logout">Logout</button>
                    </form>
                @else
                    <a class='navbar-brand' href="/login">Login</a>
                    <a class='navbar-brand' href='/register'>Register</a>
                @endauth
            </div>
        </nav>

        <div class="container">
            {{$slot}}
        </div>
    </div>
</body>
</html>