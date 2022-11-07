<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pajak extends Model
{
    use HasFactory;

    protected $table = 'pajaks';
    protected $fillable = [
        'nama_pajak',
        'satuan_pajak',
        'active',
    ];
}
