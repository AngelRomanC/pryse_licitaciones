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
        Schema::create('documento_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained()->onDelete('cascade');
            $table->string('ruta_archivo');
            $table->enum('tipo', ['principal', 'anexo']);
            $table->string('nombre_original');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_archivos');
    }
};
