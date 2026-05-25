<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_productos_table.php
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            // Las fotos irán en otra tabla si son "múltiples", o puedes guardarlas como JSON aquí.
            // Para hacerlo relacional, mejor una tabla extra de fotos, pero si el profe acepta JSON:
            $table->json('fotos')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
