<?php

namespace Tests\Feature;

use App\Models\Doc;
use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GestorArchivosTest extends TestCase
{
    use RefreshDatabase;

    public function test_foto_subir_reemplazar_y_eliminar_limpia_el_disco(): void
    {
        Storage::fake('public');

        $this->post('/photos', ['title' => 'Playa', 'photo' => UploadedFile::fake()->image('playa.jpg')])
            ->assertRedirect('/photos')->assertSessionHas('success');
        $photo = Photo::first();
        Storage::disk('public')->assertExists($photo->path);
        $this->assertStringStartsWith('photos/', $photo->path);

        $antigua = $photo->path;
        $this->put("/photos/{$photo->id}", ['title' => 'Montaña', 'photo' => UploadedFile::fake()->image('monte.png')])
            ->assertRedirect('/photos')->assertSessionHas('success');
        $photo->refresh();
        Storage::disk('public')->assertMissing($antigua);
        Storage::disk('public')->assertExists($photo->path);
        $this->assertSame('Montaña', $photo->title);

        $this->get('/photos')->assertOk()->assertSee('Montaña');

        $this->delete("/photos/{$photo->id}")->assertRedirect('/photos')->assertSessionHas('success');
        Storage::disk('public')->assertMissing($photo->path);
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_foto_valida_campos_formato_y_tamano(): void
    {
        Storage::fake('public');

        $this->post('/photos', [])->assertSessionHasErrors(['title', 'photo']);
        $this->post('/photos', ['title' => 'x', 'photo' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('photo');
        $this->post('/photos', ['title' => 'x', 'photo' => UploadedFile::fake()->image('g.jpg')->size(3000)])
            ->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('photos', 0);
    }

    public function test_doc_subir_reemplazar_y_eliminar_limpia_el_disco(): void
    {
        Storage::fake('public');

        $this->post('/docs', ['title' => 'Apuntes', 'doc' => UploadedFile::fake()->create('a.pdf', 100, 'application/pdf')])
            ->assertRedirect('/docs')->assertSessionHas('success');
        $doc = Doc::first();
        Storage::disk('public')->assertExists($doc->path);
        $this->assertStringStartsWith('docs/', $doc->path);

        $this->get('/docs')->assertOk()->assertSee('target="_blank"', false);

        $antiguo = $doc->path;
        $this->put("/docs/{$doc->id}", ['title' => 'Apuntes v2', 'doc' => UploadedFile::fake()->create('b.pdf', 100, 'application/pdf')])
            ->assertRedirect('/docs');
        $doc->refresh();
        Storage::disk('public')->assertMissing($antiguo);
        Storage::disk('public')->assertExists($doc->path);

        $this->delete("/docs/{$doc->id}")->assertRedirect('/docs');
        Storage::disk('public')->assertMissing($doc->path);
        $this->assertDatabaseCount('docs', 0);
    }

    public function test_doc_valida_campos_formato_y_tamano(): void
    {
        Storage::fake('public');

        $this->post('/docs', [])->assertSessionHasErrors(['title', 'doc']);
        $this->post('/docs', ['title' => 'x', 'doc' => UploadedFile::fake()->image('a.jpg')])
            ->assertSessionHasErrors('doc');
        $this->post('/docs', ['title' => 'x', 'doc' => UploadedFile::fake()->create('g.pdf', 3000, 'application/pdf')])
            ->assertSessionHasErrors('doc');
        $this->assertDatabaseCount('docs', 0);
    }
}
