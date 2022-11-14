<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualans';
    protected $fillable = [
        'penjualan_id',
        'produk_id',
        'qty',
        'ket',
        'disc',
        'jumlah',
    ];

    public function penjualan()
    {
        return $this->belongsTo(penjualan::class, 'id', 'penjualan_id');
    }

    public function produk()
    {
        return $this->belongsTo(produk::class, 'produk_id', 'id');
    }
}
