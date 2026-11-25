<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class DocController extends Controller
{
    // Mensajes de validación en español
    private array $mensajes = [
        'title.required' => 'El título es obligatorio.',
        'title.max'      => 'El título no puede superar los 100 caracteres.',
        'doc.required'   => 'Tienes que elegir un documento PDF.',
        'doc.mimes'      => 'Solo se permiten archivos PDF.',
        'doc.max'        => 'El PDF no puede pesar más de 2 MB.',
        'doc.uploaded'   => 'No se pudo subir el PDF (máximo 2 MB).',
    ];

    public function index()
    {
        try {
            $docs = Doc::latest()->get();
            return view('docs.index', compact('docs'));
        } catch (Throwable $e) {
            return view('docs.index', ['docs' => collect()])
                ->with('error', 'No se pudieron cargar los documentos: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:100',
                'doc'   => 'required|file|mimes:pdf|max:2048',
            ], $this->mensajes);

            // Se guarda en storage/app/public/docs
            $path = $request->file('doc')->store('docs', 'public');

            Doc::create([
                'title' => $request->title,
                'path'  => $path,
            ]);

            return redirect()->route('docs.index')->with('success', 'Documento subido correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->withInput()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Doc $doc)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:100',
                'doc'   => 'required|file|mimes:pdf|max:2048',
            ], $this->mensajes);

            $antiguo = $doc->path;
            $path = $request->file('doc')->store('docs', 'public');

            $doc->update([
                'title' => $request->title,
                'path'  => $path,
            ]);

            // Borrar el archivo antiguo para no dejar huérfanos
            Storage::disk('public')->delete($antiguo);

            return redirect()->route('docs.index')->with('success', 'Documento reemplazado correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return back()->with('error', 'Error al reemplazar el documento: ' . $e->getMessage());
        }
    }

    public function destroy(Doc $doc)
    {
        try {
            Storage::disk('public')->delete($doc->path);
            $doc->delete();

            return redirect()->route('docs.index')->with('success', 'Documento eliminado correctamente.');
        } catch (Throwable $e) {
            return back()->with('error', 'Error al eliminar el documento: ' . $e->getMessage());
        }
    }
}
