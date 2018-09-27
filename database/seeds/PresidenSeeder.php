<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresidenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('presidens')->insert([
            'name' => "Soekarno",
            'country' => "Indonesia",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '17-Aug-1945'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '17-Mar-1967'),
        ]);

        DB::table('presidens')->insert([
            'name' => "Obama",
            'country' => "USA",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Jan-2009'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '20-Jan-2017'),
        ]);


    }
}
