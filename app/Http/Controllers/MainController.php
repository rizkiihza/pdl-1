<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function handleQuery(Request $request) {
        $query_pieces = explode(" ", $request->query('query'));

        if ($query_pieces[0] === "select") {
            echo 1;
        }

        else if ($query_pieces[0] === "projection") {
            echo 2;
        }

        else if ($query_pieces[0] === "join") {
            echo 3;
        }

        else if ($query_pieces[0] === "insert") {
            $table_name = $query_pieces[count($query_pieces)-1];
            $value = $query_pieces[1];
            
            var_dump($query_pieces);
        }

        else if ($query_pieces[0] == "delete") {
            echo 5;
        }
    }

    private function insert($value, $tables) {
        // cek conflict
        $tables = DB::select('SHOW TABLES');
        var_dump($tables);
    }
}
