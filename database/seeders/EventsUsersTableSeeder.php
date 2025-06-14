<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events_users')->insert([
            [
                'event_id' => 1,
                'user_id' => 1,
            ], [
                'event_id' => 2,
                'user_id' => 2,
            ], [
                'event_id' => 3,
                'user_id' => 3,
            ],
        ]);
    }
}
