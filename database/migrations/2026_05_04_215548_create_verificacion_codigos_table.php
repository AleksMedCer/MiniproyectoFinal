<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_verificacion_codigos_table.php
    public function up(): void
    {
        Schema::create('verificacion_codigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('codigo');
            $table->timestamp('expiracion'); // <--- REVISA QUE ESTA LÍNEA ESTÉ AQUÍ Y BIEN ESCRITA
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verificacion_codigos');
    }
};
