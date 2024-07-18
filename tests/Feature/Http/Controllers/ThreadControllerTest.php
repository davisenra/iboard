<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\ThreadController;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ThreadController::class)]
class ThreadControllerTest extends TestCase
{
    use DatabaseMigrations;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function createNewThread(): void
    {
        $board = Board::factory()->create();

        $file = UploadedFile::fake()->createWithContent(
            name: 'image.jpeg',
            content: file_get_contents(__DIR__.'/../../../Fixtures/image.jpeg') ?: throw new \RuntimeException('Could not load fixture image')
        );

        $response = $this->post("/$board->route/post", [
            'subject' => 'Foo',
            'content' => 'Bar',
            'file' => $file,
        ]);

        $thread = Post::first();

        $this->assertEquals(302, $response->getStatusCode());

        $response->assertRedirectToRoute('thread.show', [
            'board' => $board->route,
            'thread' => $thread->id,
        ]);

        $this->assertNotNull($thread);
        $this->assertEquals($thread->board->id, $board->id);
        $this->assertNull($thread->post_id);
        $this->assertEquals('Bar', $thread->content);
        $this->assertEquals('Foo', $thread->subject);
        $this->assertNotNull($thread->file);
        $this->assertNotNull($thread->file_size);
        $this->assertEquals('image.jpeg', $thread->original_filename);
        $this->assertEquals('20x20', $thread->file_resolution);
    }
}
