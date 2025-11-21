<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReprogrammingFieldsToAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Para la cita NUEVA (la reprogramada)
            $table->foreignId('reprogrammed_from')->nullable()->constrained('appointments')->comment('Cita original que fue reprogramada');
            $table->foreignId('reprogrammed_by')->nullable()->constrained('users')->comment('Usuario que realizó la reprogramación');
            $table->text('reprogramming_reason')->nullable()->comment('Motivo de la reprogramación');
            
            // Para la cita ORIGINAL (la cancelada)
            $table->string('status')->default('Reservada')->change(); // Para permitir nuevo estado
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['reprogrammed_from']);
            $table->dropForeign(['reprogrammed_by']);
            $table->dropColumn(['reprogrammed_from', 'reprogrammed_by', 'reprogramming_reason']);
        });
    }
}