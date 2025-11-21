<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration normalizes the translations table from having
     * multiple language columns (spanish_word, german_word) to a
     * single row per translation with language and translation columns.
     */
    public function up(): void
    {
        // Step 1: Create new normalized table
        Schema::create('translations_new', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('word_id');
            $table->string('language', 5); // ISO 639-1 code (en, es, de, fr, etc)
            $table->text('translation');
            $table->timestamps();

            // Foreign key
            $table->foreign('word_id')
                ->references('id')
                ->on('words')
                ->onDelete('cascade');

            // Ensure one translation per language per word
            $table->unique(['word_id', 'language']);

            // Index for faster lookups
            $table->index('language');
        });

        // Step 2: Migrate existing data
        // Get all existing translations from old structure
        $oldTranslations = DB::table('translations')->get();

        foreach ($oldTranslations as $oldRecord) {
            $createdAt = $oldRecord->created_at;
            $updatedAt = $oldRecord->updated_at;

            // Insert English translation (from words table)
            $englishWord = DB::table('words')
                ->where('id', $oldRecord->word_id)
                ->value('english_word');

            if ($englishWord) {
                DB::table('translations_new')->insert([
                    'word_id' => $oldRecord->word_id,
                    'language' => 'en',
                    'translation' => $englishWord,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }

            // Insert Spanish translation if exists
            if (!empty($oldRecord->spanish_word)) {
                DB::table('translations_new')->insert([
                    'word_id' => $oldRecord->word_id,
                    'language' => 'es',
                    'translation' => $oldRecord->spanish_word,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }

            // Insert German translation if exists
            if (!empty($oldRecord->german_word)) {
                DB::table('translations_new')->insert([
                    'word_id' => $oldRecord->word_id,
                    'language' => 'de',
                    'translation' => $oldRecord->german_word,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }
        }

        // Step 3: Drop old table
        Schema::dropIfExists('translations');

        // Step 4: Rename new table to original name
        Schema::rename('translations_new', 'translations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Create old structure
        Schema::create('translations_old', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('word_id');
            $table->string('spanish_word')->nullable();
            $table->string('german_word')->nullable();
            $table->timestamps();

            $table->foreign('word_id')
                ->references('id')
                ->on('words');
        });

        // Step 2: Migrate data back (group by word_id)
        $normalizedTranslations = DB::table('translations')->get();
        $grouped = [];

        foreach ($normalizedTranslations as $translation) {
            if (!isset($grouped[$translation->word_id])) {
                $grouped[$translation->word_id] = [
                    'word_id' => $translation->word_id,
                    'spanish_word' => null,
                    'german_word' => null,
                    'created_at' => $translation->created_at,
                    'updated_at' => $translation->updated_at,
                ];
            }

            if ($translation->language === 'es') {
                $grouped[$translation->word_id]['spanish_word'] = $translation->translation;
            } elseif ($translation->language === 'de') {
                $grouped[$translation->word_id]['german_word'] = $translation->translation;
            }
            // Note: English is stored in words table, so we skip it here
        }

        // Insert grouped data
        foreach ($grouped as $record) {
            DB::table('translations_old')->insert($record);
        }

        // Step 3: Drop normalized table
        Schema::dropIfExists('translations');

        // Step 4: Rename old table back
        Schema::rename('translations_old', 'translations');
    }
};
