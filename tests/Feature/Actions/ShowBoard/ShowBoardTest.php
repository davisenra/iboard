<?php

namespace Tests\Feature\Actions\ShowBoard;

use App\Actions\ShowBoard\ShowBoard;
use App\Actions\ShowBoard\ShowBoardPayload;
use App\DataTransferObjects\Reply;
use App\DataTransferObjects\ThreadWithRecentReplies;
use App\Exception\BoardException;
use App\Models\Board;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowBoardTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function showBoard(): void
    {
        $board = Board::factory()->create();

        Post::factory()
            ->thread()
            ->count(20)
            ->for($board)
            ->has(
                factory: Post::factory()
                    ->reply()
                    ->for($board)
                    ->count(5),
                relationship: 'replies'
            )
            ->create();

        $payload = new ShowBoardPayload(
            page: 1,
            boardRoute: $board->route
        );

        $sut = new ShowBoard();
        $paginatedBoard = $sut->handle($payload);

        $this->assertEquals(1, $paginatedBoard->currentPage);
        $this->assertEquals(2, $paginatedBoard->lastPage);
        $this->assertEquals(20, $paginatedBoard->totalItems);
        $this->assertCount(10, $paginatedBoard->threads);
        $this->assertContainsOnlyInstancesOf(ThreadWithRecentReplies::class, $paginatedBoard->threads);
        $this->assertContainsOnlyInstancesOf(Reply::class, $paginatedBoard->threads[0]->recentReplies);
        $this->assertCount(3, $paginatedBoard->threads[0]->recentReplies);
    }

    #[Test]
    public function exceptionIsThrownWhenBoardDoesNotExist(): void
    {
        $payload = new ShowBoardPayload(
            page: 1,
            boardRoute: 'nonexistent'
        );

        $this->expectException(BoardException::class);
        $this->expectExceptionMessage('No board found for route: nonexistent');

        $sut = new ShowBoard();
        $sut->handle($payload);
    }
}
