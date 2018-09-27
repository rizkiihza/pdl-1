<?php

use Illuminate\Database\Seeder;

class PresidenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        B::table('presidens')->insert([
            'name' => "soekarno",
            'country' => "indonesia",
            ''
        ]);
    }
}
