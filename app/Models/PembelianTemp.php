<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianTemp extends Model
{
    use HasFactory;

    protected $table = 'pembelians_temp';
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
