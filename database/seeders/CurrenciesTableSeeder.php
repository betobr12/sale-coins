<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'USD',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('currencies')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'CAD',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('currencies')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'EUR',
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
