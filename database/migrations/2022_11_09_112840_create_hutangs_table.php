<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHutangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('tanggal_hutang');
            $table->string('bulan', 10);
            $table->string('tahun', 10);
            $table->string('ket', 50);
            $table->decimal('total', 15, 1);
            $table->decimal('kredit', 15, 1);
            $table->decimal('sisa', 15, 1);
            $table->enum('status_lunas', [0, 1])->default(0);
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
        Schema::dropIfExists('hutangs');
    }
}
