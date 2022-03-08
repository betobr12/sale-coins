<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxes')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Boleto',
            'percentage' => 1.37,
            'created_at' => \Carbon\Carbon::now(),
        ]);
        DB::table('taxes')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Cartão',
            'percentage' => 7.73,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('taxes')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Taxa de conversão para valores abaixo de R$ 3.700,00',
            'percentage' => 2,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('taxes')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Taxa de conversão para valores maiores que R$ 3.700,01',
            'percentage' => 1,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
