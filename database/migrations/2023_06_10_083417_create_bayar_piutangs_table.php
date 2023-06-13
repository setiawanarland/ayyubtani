<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBayarPiutangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bayar_piutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kios_id')->constrained('kios');
            $table->date('tanggal_bayar');
            $table->string('bulan', 10);
            $table->string('tahun', 10);
            $table->string('ket', 100);
            $table->decimal('total', 15, 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bayar_piutangs');
    }
}
