<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->date('tgl_presensi');
            $table->time('jam_in');
            $table->time('jam_out');
            $table->string('foto_in');
            $table->string('foto_out');
            $table->string('lokasi_in');
            $table->string('lokasi_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presensi');
    }
};
