<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documentos_modalidades', function (Blueprint $table) {
            //$table->id();
            // Definir las claves foráneas
            $table->foreignId('documento_id')->constrained('documentos')->onDelete('cascade');
            $table->foreignId('modalidad_id')->constrained('modalidads')->onDelete('cascade');

            // Establecer una clave primaria compuesta
            $table->primary(['documento_id', 'modalidad_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_modalidades');
    }
};
