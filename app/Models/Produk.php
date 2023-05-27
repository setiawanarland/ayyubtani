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

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id', 'produk_id');
    }

    public function stokBulanan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id', 'produk_id');
    }
}
