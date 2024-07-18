<?php

namespace Tests\Feature\Actions\NewThread;

use App\Actions\NewThread\CreateNewThread;
use App\Actions\NewThread\NewThreadPayload;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CreateNewThread::class)]
class CreateNewThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function createNewThread(): void
    {
        $board = Board::factory()->create();

        $file = new UploadedFile(__DIR__.'/../../../Fixtures/image.jpeg', 'image.jpeg');

        $payload = new NewThreadPayload(
            boardRoute: $board->route,
            subject: 'Foo',
            content: 'Bar',
            ipAddress: fake()->ipv4(),
            file: $file,
        );

        $sut = new CreateNewThread();
        $sut->handle($payload);

        Storage::disk('local')->exists($file->getFilename());

        $post = Post::first();

        $this->assertNotNull($post);
        $this->assertEquals($post->board->id, $board->id);
        $this->assertNull($post->post_id);
        $this->assertEquals('Bar', $post->content);
        $this->assertEquals('Foo', $post->subject);
        $this->assertNotNull($post->file);
        $this->assertNotNull($post->file_size);
        $this->assertEquals('image.jpeg', $post->original_filename);
        $this->assertEquals('20x20', $post->file_resolution);
    }
}
