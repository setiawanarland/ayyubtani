<?php

namespace Database\Seeders;

use App\Models\LimitPutang;
use Illuminate\Database\Seeder;

class LimitPutangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LimitPutang::create([
            'nama' => 'limit',
            'limit' => 10000000,

        ]);
    }
}
