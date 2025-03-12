<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
               'image_url' => '1/nombreImagen.jpg',
               'date' => '2025-09-11',
               'start_time' => '19:00:00',
               'end_time' => '23:00:00',
               'description' => 'Descripción del evento',
               'category_id' => 1,
               'subcategory_id' => 1,
               'owner_id' => 1,
           ],[
               'title' => 'Torneo Tekken 8',
               'location' => 'calle falsa 120',
               'image_url' => '2/nombreImagen.jpg',
               'date' => '2025-07-20',
               'start_time' => '17:00:00',
               'end_time' => '20:00:00',
               'description' => 'Descripción del evento',
               'category_id' => 2,
               'subcategory_id' => 5,
               'owner_id' => 2,
           ],[
               'title' => 'Partido amateur de baloncesto',
               'location' => 'pabellón falso 123',
               'image_url' => '3/nombreImagen.jpg',
               'date' => '2025-09-20',
               'start_time' => '10:00:00',
               'end_time' => '13:00:00',
               'description' => 'Descripción del evento',
               'category_id' => 3,
               'subcategory_id' => 7,
               'owner_id' => 3,
           ],
        ]);
    }
}
