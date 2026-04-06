<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['gasto', 'ingreso']);
            $table->unsignedSmallInteger('position')->default(0);
        });

        $gastos = [
            'Alimentación', 'Transporte', 'Servicios', 'Entretenimiento',
            'Salud', 'Ropa', 'Hogar', 'Educación', 'Restaurantes', 'Otros',
        ];

        $ingresos = ['Sueldo', 'Freelance', 'Inversiones', 'Regalo', 'Bono', 'Otros'];

        $rows = [];
        foreach ($gastos as $i => $name) {
            $rows[] = ['name' => $name, 'type' => 'gasto', 'position' => $i + 1];
        }
        foreach ($ingresos as $i => $name) {
            $rows[] = ['name' => $name, 'type' => 'ingreso', 'position' => $i + 1];
        }

        DB::table('categories')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
