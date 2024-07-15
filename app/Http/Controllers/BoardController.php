<?php

namespace App\Http\Controllers;

use App\Actions\ShowBoard\ShowBoard;
use App\Actions\ShowBoard\ShowBoardPayload;
use App\Exception\BoardException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function __construct(
        private readonly ShowBoard $handler
    ) {}

    public function show(string $boardRoute, Request $request): View
    {
        $page = (int) $request->query('page', '1');

        $payload = new ShowBoardPayload(
            page: $page,
            boardRoute: $boardRoute,
        );

        try {
            $paginatedBoard = $this->handler->handle($payload);
        } catch (BoardException) {
            abort(404);
        }

        return view('board.show', [
            'boardRoute' => $boardRoute,
            'boardName' => $paginatedBoard->boardName,
            'boardDescription' => $paginatedBoard->boardDescription,
            'threads' => $paginatedBoard->threads,
            'currentPage' => $paginatedBoard->currentPage,
            'hasMorePages' => $paginatedBoard->hasMorePages,
            'lastPage' => $paginatedBoard->lastPage,
            'totalItems' => $paginatedBoard->totalItems,
        ]);
    }
}
