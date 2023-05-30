<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokTahunan extends Model
{
    use HasFactory;

    protected $table = 'stok_tahunans';
    protected $fillable = [
        'produk_id',
        'tahun',
        'jumlah',
    ];

    public function produk()
    {
        return $this->belongsTo(produk::class, 'produk_id', 'id');
    }
}
