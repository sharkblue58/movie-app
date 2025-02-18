<?php

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
        Schema::create('cast', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('serie_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('actor_id')->constrained()->onDelete('cascade');
            $table->string('character_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cast');
    }
};
