<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PhotoController extends Controller
{
    // Mensajes de validación en español
    private array $mensajes = [
        'title.required' => 'El título es obligatorio.',
        'title.max'      => 'El título no puede superar los 100 caracteres.',
        'photo.required' => 'Tienes que elegir una imagen.',
        'photo.image'    => 'El archivo tiene que ser una imagen.',
        'photo.mimes'    => 'Formatos permitidos: jpg, jpeg, png, gif o webp.',
        'photo.max'      => 'La imagen no puede pesar más de 2 MB.',
        'photo.uploaded' => 'No se pudo subir la imagen (máximo 2 MB).',
    ];

    public function index()
    {
        try {
            $photos = Photo::latest()->get();
            return view('photos.index', compact('photos'));
        } catch (Throwable $e) {
            return view('photos.index', ['photos' => collect()])
                ->with('error', 'No se pudieron cargar las imágenes: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:100',
                'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            ], $this->mensajes);

            // Se guarda en storage/app/public/photos
            $path = $request->file('photo')->store('photos', 'public');

            Photo::create([
                'title' => $request->title,
                'path'  => $path,
            ]);

            return redirect()->route('photos.index')->with('success', 'Imagen subida correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->withInput()->with('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Photo $photo)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:100',
                'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            ], $this->mensajes);

            $antigua = $photo->path;
            $path = $request->file('photo')->store('photos', 'public');

            $photo->update([
                'title' => $request->title,
                'path'  => $path,
            ]);

            // Borrar el archivo antiguo para no dejar huérfanos
            Storage::disk('public')->delete($antigua);

            return redirect()->route('photos.index')->with('success', 'Imagen reemplazada correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->with('error', 'Error al reemplazar la imagen: ' . $e->getMessage());
        }
    }

    public function destroy(Photo $photo)
    {
        try {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();

            return redirect()->route('photos.index')->with('success', 'Imagen eliminada correctamente.');
        } catch (Throwable $e) {
            return back()->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }
}
