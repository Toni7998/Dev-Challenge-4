<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Fecha de la reserva
            $table->string('name'); // Nombre de la persona
            $table->string('email'); // Correo electrónico
            $table->string('phone'); // Teléfono
            $table->string('course'); // Curso o actividad reservada
            $table->string('hour')->nullable(); // Hora de la reserva
            $table->timestamps(); // created_at y updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
