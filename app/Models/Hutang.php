<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    use HasFactory;

    protected $table = 'hutangs';
    protected $fillable = [
        'pembelian_id',
        'tanggal_bayar',
        'bulan',
        'tahun',
        'ket',
        'debet',
        'kredit',
        'sisa',
        'status',
    ];
}
