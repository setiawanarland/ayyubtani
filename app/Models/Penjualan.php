<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';
    protected $fillable = [
        'kios_id',
        'invoice',
        'tanggal_jual',
        'bulan',
        'tahun',
        'dpp',
        'ppn',
        'total_disc',
        'grand_total',
    ];

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'id');
    }

    public function kios()
    {
        return $this->belongsTo(kios::class, 'kios_id', 'id');
    }
}
