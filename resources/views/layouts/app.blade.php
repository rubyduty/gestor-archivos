<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Mi Nube</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: #f4f6f9; color: #222; }
        header { background: #1f2937; color: #fff; padding: 14px 24px; display: flex; align-items: center; gap: 24px; }
        header h1 { font-size: 20px; margin: 0; }
        nav a { color: #cbd5e1; text-decoration: none; margin-right: 16px; padding: 6px 10px; border-radius: 6px; }
        nav a.activo, nav a:hover { background: #374151; color: #fff; }
        main { max-width: 1000px; margin: 24px auto; padding: 0 16px; }
        .alerta { padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; }
        .ok { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .mal { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .mal ul { margin: 4px 0 0; padding-left: 18px; }
        .caja { background: #fff; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
        input[type=text] { padding: 7px; border: 1px solid #ccc; border-radius: 5px; width: 100%; }
        input[type=file] { font-size: 13px; }
        button { padding: 7px 14px; border: 0; border-radius: 5px; cursor: pointer; color: #fff; background: #2563eb; }
        button.rojo { background: #dc2626; }
        button.gris { background: #4b5563; }
        .fila { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .fila > * { flex: 1; min-width: 160px; }
        .fila > button { flex: 0; }
    </style>
</head>
<body>
<header>
    <h1>☁️ Mi Nube</h1>
    <nav>
        <a href="{{ route('photos.index') }}" class="{{ request()->routeIs('photos.*') ? 'activo' : '' }}">🖼️ Galería (/photos)</a>
        <a href="{{ route('docs.index') }}" class="{{ request()->routeIs('docs.*') ? 'activo' : '' }}">📄 Documentos (/docs)</a>
    </nav>
</header>
<main>
    @if (session('success'))
        <div class="alerta ok">{{ session('success') }}</div>
    @endif
    @if (session('error') || isset($error))
        <div class="alerta mal">{{ session('error') ?? $error }}</div>
    @endif
    @if ($errors->any())
        <div class="alerta mal">
            Revisa el formulario:
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
