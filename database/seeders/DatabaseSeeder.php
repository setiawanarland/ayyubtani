<?php

namespace Database\Seeders;

use App\Models\Produk;
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

        Produk::create([
            'nama_produk'    => 'abolisi 865 sl',
            'kemasan'    => '200 ml x 48',
            'satuan' => 'pcs',
            'jumlah_perdos' => 48,
            'harga_beli' => 21400,
            'harga_jual' => 22700,
            'harga_perdos' => 1089600,
        ]);
    }
}
