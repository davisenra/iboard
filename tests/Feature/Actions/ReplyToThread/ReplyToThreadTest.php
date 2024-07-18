<?php

namespace Tests\Feature\Actions\ReplyToThread;

use App\Actions\ReplyToThread\ReplyPayload;
use App\Actions\ReplyToThread\ReplyToThread;
use App\Exception\PostException;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ReplyToThread::class)]
class ReplyToThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    #[Test]
    public function replyToThreadWithImage(): void
    {
        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->thread()->create();

        $userIp = fake()->ipv4();
        $file = new UploadedFile(__DIR__.'/../../../Fixtures/image.jpeg', 'image.jpeg');

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            ipAddress: $userIp,
            file: $file,
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        Storage::disk('local')->exists($file->getFilename());

        $reply = $thread->replies()->first();

        $this->assertNotNull($reply);
        $this->assertEquals('Foo', $reply->content);
        $this->assertEquals($userIp, $reply->ip_address);
        $this->assertNull($reply->options);
        $this->assertNotNull($reply->file_size);
        $this->assertEquals('image.jpeg', $reply->original_filename);
        $this->assertEquals('20x20', $reply->file_resolution);
    }

    #[Test]
    public function replyToThreadWithoutImage(): void
    {
        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->create();

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            ipAddress: fake()->ipv4(),
            file: null,
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        $this->assertDatabaseHas('posts', [
            'post_id' => $payload->threadId,
            'content' => $payload->content,
            'file' => null,
        ]);
    }

    #[Test]
    public function replyingBumpsTheThread(): void
    {
        $originalLastPostedAt = now()->subtract('10 days');

        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->create(['last_replied_at' => $originalLastPostedAt]);

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            ipAddress: fake()->ipv4(),
            file: null,
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        $thread->refresh();

        $this->assertTrue($thread->last_replied_at > $originalLastPostedAt);
    }

    #[Test]
    public function replyWithSageDoesNotBumpTheThread(): void
    {
        $fiveMinutesAgo = now()->subtract('5 minutes');

        $board = Board::factory()->create();
        $thread = Post::factory()->for($board)->create(['last_replied_at' => $fiveMinutesAgo]);

        $payload = new ReplyPayload(
            threadId: $thread->id,
            content: 'Foo',
            ipAddress: fake()->ipv4(),
            options: 'sage',
            file: null
        );

        $sut = new ReplyToThread();
        $sut->handle($payload);

        $thread->refresh();

        $this->assertEquals($fiveMinutesAgo->getTimestamp(), $thread->last_replied_at->getTimestamp());
    }

    #[Test]
    public function exceptionIsThrownWhenReplyingToNonExistentThread(): void
    {
        $payload = new ReplyPayload(
            threadId: 420,
            content: 'Foo',
            ipAddress: fake()->ipv4(),
            file: null,
        );

        $this->expectException(PostException::class);
        $this->expectExceptionMessage('No thread found for id: 420');

        $sut = new ReplyToThread();
        $sut->handle($payload);
    }
}
