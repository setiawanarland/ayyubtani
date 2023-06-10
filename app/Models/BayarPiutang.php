<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BayarPiutang extends Model
{
    use HasFactory;

    protected $table = 'bayar_piutangs';
    protected $fillable = [
        'kios_id',
        'tanggal_bayar',
        'bulan',
        'tahun',
        'ket',
        'total',
    ];

    public function kios()
    {
        return $this->belongsTo(Kios::class, 'kios_id', 'id');
    }
}
