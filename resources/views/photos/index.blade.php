@extends('layouts.app')

@section('title', 'Galería')

@section('content')
    <div class="caja">
        <h2>Subir imagen</h2>
        <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data" class="fila">
            @csrf
            <input type="text" name="title" placeholder="Título" value="{{ old('title') }}" required>
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit">Subir</button>
        </form>
    </div>

    <h2>Galería de imágenes ({{ $photos->count() }})</h2>

    @forelse ($photos as $photo)
        <div class="caja" style="display:flex; gap:16px; align-items:flex-start;">
            <img src="{{ asset('storage/' . $photo->path) }}" alt="{{ $photo->title }}"
                 style="width:220px; height:160px; object-fit:cover; border-radius:6px;">
            <div style="flex:1;">
                <h3 style="margin-top:0;">{{ $photo->title }}</h3>

                <form action="{{ route('photos.update', $photo) }}" method="POST" enctype="multipart/form-data" class="fila">
                    @csrf
                    @method('PUT')
                    <input type="text" name="title" value="{{ $photo->title }}" required>
                    <input type="file" name="photo" accept="image/*" required>
                    <button type="submit" class="gris">Reemplazar</button>
                </form>

                <form action="{{ route('photos.destroy', $photo) }}" method="POST" style="margin-top:10px;"
                      onsubmit="return confirm('¿Eliminar esta imagen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rojo">Eliminar</button>
                </form>
            </div>
        </div>
    @empty
        <p>Todavía no hay imágenes.</p>
    @endforelse
@endsection
