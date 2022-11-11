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
            'nama_pajak' => 'ppn 11%',
            'satuan_pajak' => 11,
            'active' => '1',

        ]);

        Produk::create(
            [
                'nama_produk'    => 'abolisi 865 sl',
                'kemasan'    => '200 ml x 48',
                'jumlah_perdos' => 48,
                'satuan' => 'pcs',
                'harga_beli' => 21400,
                'harga_jual' => 22700,
                'harga_perdos' => 1089600,
            ],

        );

        Produk::create(
            [
                'nama_produk'    => 'abolisi 865 sl',
                'kemasan'    => '400 ml x 24',
                'jumlah_perdos' => 24,
                'satuan' => 'pcs',
                'harga_beli' => 40600,
                'harga_jual' => 43000,
                'harga_perdos' => 1032000,
            ]

        );

        Produk::create(
            [
                'nama_produk'    => 'supremo 480 sl',
                'kemasan'    => '4 l x 6',
                'satuan' => 'pcs',
                'jumlah_perdos' => 6,
                'harga_beli' => 399100,
                'harga_jual' => 423000,
                'harga_perdos' => 2538000,
            ]

        );

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
