<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Cancelado',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('status')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Pago',
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('status')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Aguardando Aprovação',
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
