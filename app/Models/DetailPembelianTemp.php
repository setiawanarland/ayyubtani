<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelianTemp extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelians_temp';
    protected $fillable = [
        'produk_id',
        'qty',
        'ket',
        'disc',
        'jumlah',
    ];

    public function produk()
    {
        return $this->hasOne('App\Models\Produk', 'id', 'id_produk');
    }
}
