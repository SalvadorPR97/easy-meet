<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contact_categories')->insert([
            [
                'name' => 'Error',
            ],[
                'name' => 'Queja',
            ],[
                'name' => 'Sugerencia',
            ],[
                'name' => 'Otro',
            ],
        ]);
    }
}
