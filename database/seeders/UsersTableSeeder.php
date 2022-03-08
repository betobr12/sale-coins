<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * NÍVEL DO USUÁRIO
         * @var access_level
         * 1 => USUÁRIO COMUM
         * 2 => ADMINISTRADOR
         */

        DB::table('users')->insert([
            'name'          => 'Roberto',
            'email'         => 'betobr12@yahoo.com.br',
            'cpf_cnpj'      => '39393953074',
            'access_level'  => 2,
            'uuid'          => Str::orderedUuid(),
            'password'      => Hash::make('123456'),
            'created_at'    => \Carbon\Carbon::now(),
        ]);

        DB::table('users')->insert([
            'name'          => 'Admin',
            'email'         => 'admin@admin.com.br',
            'cpf_cnpj'      => '37180291030',
            'access_level'  => 1,
            'uuid'          => Str::orderedUuid(),
            'password'      => Hash::make('123456'),
            'created_at'    => \Carbon\Carbon::now(),
        ]);
    }
}
