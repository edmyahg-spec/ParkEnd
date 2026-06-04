<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'ParkEnd')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0d6efd">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded px-3 mb-4">

        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            ParkEnd
        </a>

        <div class="navbar-nav">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
            <a class="nav-link" href="{{ route('atracciones.index') }}">Atracciones</a>
            <a class="nav-link" href="{{ route('visitas.index') }}">Visitas</a>
            <a class="nav-link" href="{{ route('analisis.index') }}">Análisis del Parque</a>
        </div>

        <div class="ms-auto">
            <div class="dropdown">
                <a
                    class="nav-link dropdown-toggle text-white fw-bold d-flex align-items-center"
                    href="#"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">

                    {{ Auth::user()->name }}

                    @if(Auth::user()->descuento == 20)
                        <span class="badge bg-success ms-2">Premium (20%)</span>
                    @elseif(Auth::user()->descuento == 10)
                        <span class="badge bg-warning text-dark ms-2">VIP (10%)</span>
                    @elseif(Auth::user()->descuento == 5)
                        <span class="badge bg-info text-dark ms-2">Frecuente (5%)</span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('perfil.index') }}">
                            Mi Perfil
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                Cerrar sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    @endauth

    @yield('content')

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Service Worker PWA --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js')
                .then(function () {
                    console.log('Service Worker registrado correctamente');
                })
                .catch(function (error) {
                    console.log('Error al registrar Service Worker:', error);
                });
        });
    }
</script>

</body>
</html>