<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            [
                'title' => 'Concierto Mago de Oz',
                'location' => 'calle falsa 123',
                'city' => 'Málaga',
                'image_url' => '1/nombreImagen.jpg',
                'date' => '2025-09-11',
                'start_time' => '19:00:00',
                'end_time' => '23:00:00',
                'min_participants' => 2,
                'max_participants' => 10,
                'price' => 20,
                'description' => 'Descripción del evento',
                'category_id' => 1,
                'subcategory_id' => 1,
                'owner_id' => 1,
            ], [
                'title' => 'Torneo Tekken 8',
                'location' => 'calle falsa 120',
                'city' => 'Málaga',
                'image_url' => '2/nombreImagen.jpg',
                'date' => '2025-07-20',
                'start_time' => '17:00:00',
                'end_time' => '20:00:00',
                'min_participants' => 10,
                'max_participants' => 20,
                'price' => 5,
                'description' => 'Descripción del evento',
                'category_id' => 2,
                'subcategory_id' => 5,
                'owner_id' => 2,
            ], [
                'title' => 'Partido amateur de baloncesto',
                'location' => 'pabellón falso 123',
                'city' => 'Marbella',
                'image_url' => '3/nombreImagen.jpg',
                'date' => '2025-09-20',
                'start_time' => '10:00:00',
                'end_time' => '13:00:00',
                'min_participants' => 4,
                'max_participants' => 10,
                'price' => 1,
                'description' => 'Descripción del evento',
                'category_id' => 3,
                'subcategory_id' => 7,
                'owner_id' => 3,
            ],
        ]);
    }
}
