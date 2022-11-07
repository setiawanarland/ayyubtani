<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';
    protected $fillable = [
        'nama_produk',
        'kemasan',
        'satuan',
        'jumlah_perdos',
        'harga_beli',
        'harga_jual',
        'harga_perdos',
        'stok',
    ];

    public function pembelianTemp()
    {
        return $this->hasOne('App\Models\DetailPembelianTemp', 'id_produk', 'id');
    }
}
