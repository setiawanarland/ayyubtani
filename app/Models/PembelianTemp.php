<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianTemp extends Model
{
    use HasFactory;

    protected $table = 'pembelian_temps';
    protected $fillable = [
        'supplier_id',
        'invoice',
        'tanggal_beli',
        'dpp',
        'ppn',
        'disc',
        'grand_total',
    ];
}
