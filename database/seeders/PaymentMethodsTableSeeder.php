<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentMethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Boleto',
            'tax_id' => 1,
            'created_at' => \Carbon\Carbon::now(),
        ]);

        DB::table('payment_methods')->insert([
            'uuid' => Str::orderedUuid(),
            'description' => 'Cartão',
            'tax_id' => 2,
            'created_at' => \Carbon\Carbon::now(),
        ]);
    }
}
