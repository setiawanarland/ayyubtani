<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk', 100);
            $table->string('kemasan', 50);
            $table->float('jumlah_perdos');
            $table->string('satuan', 10);
            $table->bigInteger('harga_beli');
            $table->bigInteger('harga_jual');
            $table->bigInteger('harga_perdos');
            $table->integer('stok')->default(0);
            $table->bigInteger('qty')->default(0);
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
        Schema::dropIfExists('produks');
    }
}
