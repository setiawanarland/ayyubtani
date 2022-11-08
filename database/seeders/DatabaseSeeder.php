<?php

namespace Database\Seeders;

use App\Models\Kios;
use App\Models\pajak;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username'    => 'arlandsetiawan',
            'email'    => 'john_smith@gmail.com',
            'password'   =>  Hash::make('password'),
        ]);

        pajak::create([
            'nama_pajak' => 'pajak 10%',
            'satuan_pajak' => 10,
            'active' => '1',

        ]);

        Produk::create([
            'nama_produk'    => 'abolisi 865 sl',
            'kemasan'    => '200 ml x 48',
            'satuan' => 'pcs',
            'jumlah_perdos' => 48,
            'harga_beli' => 21400,
            'harga_jual' => 22700,
            'harga_perdos' => 1089600,
        ]);

        Supplier::create([
            'nama_supplier'    => 'pt. tiga madiri',
            'alamat'    => 'jl. veteran',
            'npwp' => '09.254.294.3-407.000',
            'nik' => '7304072610950002',
        ]);

        Kios::create([
            'nama_kios'    => 'tani beru',
            'pemilik'    => 'h. ridwan',
            'kabupaten'    => 'bantaeng',
            'alamat'    => 'jl. somba upu',
            'npwp' => '09.254.294.3-407.000',
            'nik' => '7304072610950002',
        ]);
    }
}
