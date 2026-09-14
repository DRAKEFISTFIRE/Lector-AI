<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_form_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_form_id')->constrained()->cascadeOnDelete();
            $table->text('prompt');
            $table->text('expected_answer')->nullable();
            $table->enum('type', ['flashcard', 'pregunta_abierta'])->default('flashcard');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_form_items');
    }
};