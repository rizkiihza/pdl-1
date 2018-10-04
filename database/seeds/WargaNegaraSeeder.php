<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WargaNegaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warga_negaras')->insert([
            'name' => 'Anton',
            'country' => 'Indonesia',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '15-Sep-1945'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '23-Jan-1950')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Anton',
            'country' => 'Indonesia',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '15-Sep-2045'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '23-Jan-2050')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Toni',
            'country' => 'Indonesia',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Apr-1947'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Aug-1979')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Soekarno',
            'country' => 'Indonesia',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '19-Jun-1901'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '21-Jun-1970')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Soeharto',
            'country' => 'Indonesia',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '8-Jun-1921'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '27-Jan-2008')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => "Susilo Bambang Yudhoyono",
            'country' => "Indonesia",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '9-Sep-1949'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Oct-2018'),
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Anton',  
            'country' => 'USA',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '24-Jan-1950'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '30-Apr-1980')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'John',
            'country' => 'USA',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '15-Oct-1996'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '19-Feb-2006')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Huffman',
            'country' => 'USA',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '12-Nov-1985'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '15-Dec-2013')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'John',
            'country' => 'France',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '20-Feb-2006'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '19-Feb-2014')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Samuel',
            'country' => 'France',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '17-May-2007'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Apr-2012')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => 'Barrack Obama',
            'country' => 'USA',
            'valid_start' => DateTime::createFromFormat('j-M-Y', '17-May-1960'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Apr-2018')
        ]);
        DB::table('warga_negaras')->insert([
            'name' => "George Bush",
            'country' => "USA",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '6-Jul-1946'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Oct-2018'),
        ]);
        DB::table('warga_negaras')->insert([
            'name' => "Francois Hollande",
            'country' => "France",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '12-Aug-1954'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '10-Oct-2018'),
        ]);

        DB::table('warga_negaras')->insert([
            'name' => "Nicolas Sarkozy",
            'country' => "France",
            'valid_start' => DateTime::createFromFormat('j-M-Y', '28-Jan-1955'),
            'valid_end' => DateTime::createFromFormat('j-M-Y', '15-May-2017'),
        ]);
    }
}
