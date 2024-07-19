<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\BoardController;
use App\Models\Board;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(BoardController::class)]
class BoardControllerTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function viewBoard(): void
    {
        $board = Board::factory()->create();

        $response = $this->get("/$board->route");

        $this->assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    public function notFoundWhenBoardDoesNotExist(): void
    {
        $response = $this->get('/b');

        $this->assertEquals(404, $response->getStatusCode());
    }
}
