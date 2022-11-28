<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailPembeliansTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_pembelians_temp', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('pembelian_temp_id')->default(1)->constrained('pembelian_temps');
            $table->foreignId('produk_id')->constrained('produks');
            $table->float('qty');
            $table->string('ket', 50);
            $table->integer('disc');
            $table->bigInteger('jumlah');
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
        Schema::dropIfExists('detail_pembelians_temp');
    }
}
