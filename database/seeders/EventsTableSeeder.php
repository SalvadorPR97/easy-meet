<?php

namespace Database\Seeders;

use App\Models\Event;
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
                'location' => '2, Alameda Principal, Centro Histórico, Centro, Málaga, Málaga-Costa del Sol, Málaga, Andalucía, 29005, España',
                'latitude' => 36.7182332,
                'longitude' => -4.4215922,
                'city' => 'Málaga',
                'image_url' => '/storage/events_images/1/JguiyH1SL15YPCyqag6KoTwkZPNIUS7EKu9axOvH.jpg',
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
                'location' => 'Compás de la Victoria, Calle Compás de la Victoria, Cristo de la Epidemia, Centro, Málaga, Málaga-Costa del Sol, Málaga, Andalucía, 29012, España',
                'latitude' => 36.7273785,
                'longitude' => -4.413595,
                'city' => 'Teruel',
                'image_url' => '/storage/events_images/2/zc2NxTI3y4f9QxGYnHlHFUUum6v4Uqs6EDGhpBqs.jpg',
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
                'location' => '2, Alameda Principal, Centro Histórico, Centro, Málaga, Málaga-Costa del Sol, Málaga, Andalucía, 29005, España',
                'latitude' => 36.7182332,
                'longitude' => -4.4215922,
                'city' => 'Málaga',
                'image_url' => '/storage/events_images/3/EOrzBHH5XKVChzWkviGgMHNb147K7jlGHQHwNr0h.jpg',
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
            ]
        );
    }
}
