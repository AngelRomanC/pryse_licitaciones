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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_documento');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade'); // Clave foránea
            $table->foreignId('tipo_documento_id')->constrained('tipo_de_documentos')->onDelete('cascade'); // Clave foránea
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null'); // Clave foránea // Estado opcional
            $table->foreignId('departamento_id')->constrained(table: 'departamentos')->onDelete('cascade'); // Clave foránea
            $table->date('fecha_revalidacion')->nullable();
            $table->date('fecha_vigencia')->nullable();
            $table->foreignId('modalidad_id')->nullable()->constrained(table: 'departamentos')->onDelete('set null'); // Clave foránea
            $table->string('ruta_documento')->nullable();
            $table->string('ruta_documento_anexo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
