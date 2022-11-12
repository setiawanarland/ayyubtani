<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualanTemp extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan_temps';
    protected $fillable = [
        'produk_id',
        'qty',
        'ket',
        'disc',
        'jumlah',
    ];
}
