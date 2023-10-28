<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => config('test_data.userid'),
            'created_at' => '2000-01-01 00:00:00',
            'updated_at' => '2000-01-01 00:00:00',
        ]);
    }
}
