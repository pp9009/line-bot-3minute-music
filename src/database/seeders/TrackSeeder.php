<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tracks')->insert([
            'external_url' => 'https://open.spotify.com/track/' . Str::random(10),
            'artists' => Str::random(10),
            'popularity' => 001,
            'duration_ms' => 60000 * 3,
            'isrc' => 'jp' . Str::random(10),
            'created_at' => '2000-01-01 00:00:00',
            'updated_at' => '2000-01-01 00:00:00',
        ]);
    }
}
