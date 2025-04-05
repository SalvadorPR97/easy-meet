<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubcategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subcategories')->insert([
            [
                'name' => 'Rock',
                'category_id' => 1,
            ], [
                'name' => 'Jazz',
                'category_id' => 1,
            ], [
                'name' => 'Electrónica',
                'category_id' => 1,
            ], [
                'name' => 'Salón del manga',
                'category_id' => 2,
            ], [
                'name' => 'Torneo',
                'category_id' => 2,
            ], [
                'name' => 'Presentación',
                'category_id' => 2,
            ], [
                'name' => 'Baloncesto',
                'category_id' => 3,
            ], [
                'name' => 'Fútbol',
                'category_id' => 3,
            ], [
                'name' => 'Pádel',
                'category_id' => 3,
            ],
        ]);
    }
}
