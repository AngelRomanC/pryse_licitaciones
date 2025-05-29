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
        Schema::create('modalidad_licitacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licitacion_id')->constrained()->onDelete('cascade');
            $table->foreignId('modalidad_id')->constrained('modalidads')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidad_licitacion');
    }
};
