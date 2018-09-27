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
            $table = $query_pieces[1]; 
            $where = $query_pieces[2]; 
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
            $first_table = $query_pieces[1]; 
            $second_table = $query_pieces[2]; 
            $result = $this->join($first_table, $second_table);
            echo $result;
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
        $sql_where = str_replace("&", " AND ", $where);
        $result = DB::table($table)
                ->whereRaw($sql_where)
                ->get();
        return $result;
    }

    private function join($first_table, $second_table) {
        $result = DB::table($first_table)
            ->join($second_table, $first_table.".country", "=", $second_table.".country")
            ->select($first_table.".name AS ".$first_table."_name", 
                $second_table.".name AS ".$second_table."_name", 
                $second_table.".country",
                $first_table.".valid_start AS ".$first_table."_valid_start", 
                $second_table.".valid_start AS ".$second_table."_valid_start", 
                $first_table.".valid_end AS ".$first_table."_valid_end", 
                $second_table.".valid_end AS ".$second_table."_valid_end")
            ->get();

        return $result;
    }
}
