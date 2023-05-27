<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBulanan extends Model
{
    use HasFactory;

    protected $table = 'stok_bulanan';
    protected $fillable = [
        'produk_id',
        'tahun',
        'bulan',
        'jumlah',
    ];

    public function produk()
    {
        return $this->belongsTo(produk::class, 'produk_id', 'id');
    }
}
