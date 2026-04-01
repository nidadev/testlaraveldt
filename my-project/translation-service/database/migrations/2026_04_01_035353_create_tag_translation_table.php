<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tag_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('translation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['tag_id','translation_id']); // optional, prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translation');
    }
};