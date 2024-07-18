<?php

use App\Models\Board;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Board::class)->constrained();
            $table->foreignIdFor(Post::class)->nullable()->constrained();
            $table->text('content');
            $table->string('ip_address');
            $table->string('subject')->nullable();
            $table->string('options')->nullable();
            $table->string('file')->nullable();
            $table->string('file_size')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('file_resolution')->nullable();
            $table->timestamp('published_at');
            $table->timestamp('last_replied_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
