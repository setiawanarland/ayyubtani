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
        'kios_id',
        'tanggal_piutang',
        'bulan',
        'tahun',
        'ket',
        'total',
        'kredit',
        'sisa',
        'status',
    ];

    public function kios()
    {
        return $this->belongsTo(Kios::class, 'kios_id', 'id');
    }
}
