<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('users')->insert([
            [
                'name' => 'Salvador',
                'surname' => 'Pérez Ranchal',
                'username' => 'Xalvix',
                'email' => 's.perezranchal@gmail.com',
                'birthdate' => '1997-03-10',
                'password' => Hash::make('No12345.'),

            ], [
                'name' => 'Juan',
                'surname' => 'Pérez Gómez',
                'username' => 'juanxo123',
                'email' => 'juanxo@gmail.com',
                'birthdate' => '1987-03-10',
                'password' => Hash::make('No12345.'),

            ], [
                'name' => 'María',
                'surname' => 'Jiménez Llum',
                'username' => 'mery123',
                'email' => 'mery@gmail.com',
                'birthdate' => '1967-03-10',
                'password' => Hash::make('No12345.'),

            ], [
                'name' => 'Encarna',
                'surname' => 'Vales Libro',
                'username' => 'encarna123',
                'email' => 'encarna@gmail.com',
                'birthdate' => '2000-03-10',
                'password' => Hash::make('No12345.'),

            ], [
                'name' => 'Pablo',
                'surname' => 'Motos',
                'username' => 'enanomove',
                'email' => 'enanomove@gmail.com',
                'birthdate' => '1966-03-10',
                'password' => Hash::make('No12345.'),

            ],
        ]);
    }
}
