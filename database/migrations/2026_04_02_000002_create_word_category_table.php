<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_category', function(Blueprint $table) {
            $table->foreignId('word_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['word_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_category');
    }
};
