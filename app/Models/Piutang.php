<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'piutangs';
    protected $fillable = [
        'penjualan_id',
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
