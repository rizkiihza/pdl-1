<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class MainController extends Controller
{
    public function handleQuery(Request $request) {
        $query_pieces = explode(" ", $request->query('query'));

        if ($query_pieces[0] === "select") {
            $table = $query_pieces[2]; 
            $where = $query_pieces[3]; 
            $result = $this->select($table, $where);
            echo $result;
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
            $table_name = $query_pieces[count($query_pieces)-1];
            $value = $query_pieces[1];

            var_dump($query_pieces);
        }

        else if ($query_pieces[0] == "delete") {
            echo 5;
        }

        else if ($query_pieces[0] == "timeslice") {
            $table = $query_pieces[1];
            $date = DateTime::createFromFormat('j-M-Y', $query_pieces[2]);
            $this->timeslice($table, $date);
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
            for ($idx = 0; $idx < count($result) - 1; $idx++) {
                echo json_encode($result[$idx]) . '<br>';
            }
        }
    }

    private function insert($value, $tables) {
        // cek conflict
        $tables = DB::select('SHOW TABLES');
        var_dump($tables);
    }

    private function timeslice($table, $date) {
        $result = DB::table($table)->
            where('valid_start', '<=', $date)->
            where('valid_end', '>=', $date)->
            get();

        foreach ($result as $row) {
            echo json_encode($row) . '<br>';
        }
    }

    private function select($table, $where) {
        $where_clause = explode("&", $where);
        $sql_where = "";
        foreach ($where_clause as $condition) {
            $sql_where = $sql_where.$condition." AND ";
        }

        $sql_where = substr($sql_where, 0, -5);

        $result = DB::table($table)
                ->whereRaw($sql_where)
                ->get();
        return $result;
    }
}
