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
        Schema::create('archivo_licitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licitacion_id')->constrained()->onDelete('cascade');
            $table->foreignId('documento_archivo_id')->constrained()->onDelete('cascade');
            $table->enum('tipo', ['tecnico', 'legal']); // Para diferenciar tipo de documento
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_licitacion');
    }
};
