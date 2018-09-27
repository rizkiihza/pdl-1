<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function handleQuery($query_string) {
        $query_pieces = explode(" ", $query_string);

        if ($query_pieces[0] === "select") {
            echo 1;
        }

        else if ($query_pieces[0] === "projection") {
            if (count($query_pieces) != 3) {
                return redirect('/');
            }

            $columns = $query_pieces[1];
            $table = $query_pieces[2];

            $this->projection($columns, $table);
        }

        else if ($query_pieces[0] === "join") {
            echo 3;
        }

        else if ($query_pieces[0] === "insert") {
            echo 4;
        }

        else if ($query_pieces[0] == "delete") {
            echo 5;
        }
    }

    private function projection($column, $table) {
        if ($column == "*") {
            $result = DB::table($table)->get();
            foreach ($result as $row) {
                echo json_encode($row) . '<br>';
            }
        }

        else {
            $result = DB::table($table)->select($column, 'valid_start', 'valid_end')->get();
            foreach ($result as $row) {
                echo json_encode($row) . '<br>';
            }
        }
    }
}
