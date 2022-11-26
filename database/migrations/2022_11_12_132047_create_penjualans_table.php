<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kios_id')->constrained('kios');
            $table->integer('pembayaran_id')->unsigned();
            $table->string('invoice', 100);
            $table->date('tanggal_jual');
            $table->string('bulan', 10);
            $table->string('tahun', 10);
            $table->decimal('dpp', 15, 1);
            $table->decimal('ppn', 15, 1);
            $table->decimal('total_disc', 15, 1);
            $table->decimal('grand_total', 15, 1);
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
        Schema::dropIfExists('penjualans');
    }
}
