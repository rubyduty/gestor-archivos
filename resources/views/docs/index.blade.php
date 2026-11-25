@extends('layouts.app')

@section('title', 'Documentos')

@section('content')
    <div class="caja">
        <h2>Subir documento PDF</h2>
        <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data" class="fila">
            @csrf
            <input type="text" name="title" placeholder="Título" value="{{ old('title') }}" required>
            <input type="file" name="doc" accept="application/pdf" required>
            <button type="submit">Subir</button>
        </form>
    </div>

    <h2>Documentos ({{ $docs->count() }})</h2>

    @forelse ($docs as $doc)
        <div class="caja">
            <h3 style="margin-top:0;">
                📄 <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" rel="noopener">{{ $doc->title }}</a>
            </h3>

            <form action="{{ route('docs.update', $doc) }}" method="POST" enctype="multipart/form-data" class="fila">
                @csrf
                @method('PUT')
                <input type="text" name="title" value="{{ $doc->title }}" required>
                <input type="file" name="doc" accept="application/pdf" required>
                <button type="submit" class="gris">Reemplazar</button>
            </form>

            <form action="{{ route('docs.destroy', $doc) }}" method="POST" style="margin-top:10px;"
                  onsubmit="return confirm('¿Eliminar este documento?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rojo">Eliminar</button>
            </form>
        </div>
    @empty
        <p>Todavía no hay documentos.</p>
    @endforelse
@endsection
