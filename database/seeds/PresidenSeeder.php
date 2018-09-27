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
            'name' => "Soeharto",
            'country' => "Indonesia",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '17-Mar-1967'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '21-Mar-1998'),
        ]);

        DB::table('presidens')->insert([
            'name' => "Susilo Bambang Yudhoyono",
            'country' => "Indonesia",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Oct-2004'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '20-Oct-2014'),
        ]);

        DB::table('presidens')->insert([
            'name' => "Barrack Obama",
            'country' => "USA",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Jan-2009'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '20-Jan-2017'),
        ]);

        DB::table('presidens')->insert([
            'name' => "George Bush",
            'country' => "USA",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Jan-2001'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '20-Jan-2009'),
        ]);

        DB::table('presidens')->insert([
            'name' => "Francois Hollande",
            'country' => "France",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '15-May-2012'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '14-May-2017'),
        ]);

        DB::table('presidens')->insert([
            'name' => "Nicolas Sarkozy",
            'country' => "France",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '16-May-2007'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '15-May-2012'),
        ]);
    }
}
