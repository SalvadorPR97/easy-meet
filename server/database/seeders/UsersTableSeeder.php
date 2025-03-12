<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'age' => '27',
                'password' => 'No12345.',

            ], [
                'name' => 'Juan',
                'surname' => 'Pérez Gómez',
                'username' => 'juanxo123',
                'email' => 'juanxo@gmail.com',
                'age' => '22',
                'password' => 'No12345.',

            ], [
                'name' => 'María',
                'surname' => 'Jiménez Llum',
                'username' => 'mery123',
                'email' => 'mery@gmail.com',
                'age' => '30',
                'password' => 'No12345',

            ], [
                'name' => 'Encarna',
                'surname' => 'Vales Libro',
                'username' => 'encarna123',
                'email' => 'encarna@gmail.com',
                'age' => '40',
                'password' => 'No12345',

            ], [
                'name' => 'Pablo',
                'surname' => 'Motos',
                'username' => 'enanomove',
                'email' => 'enanomove@gmail.com',
                'age' => '50',
                'password' => 'No12345',

            ],
        ]);
    }
}
