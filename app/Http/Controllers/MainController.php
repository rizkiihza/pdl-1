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
            $result = DB::table($table)->orderBy($column)->orderBy('valid_start')->select($column, 'valid_start', 'valid_end')->get();

            $idx = 0;
            $compressed_result = array();

            foreach ($result as $row) {
                echo json_encode($row) . '<br>';
            }

            echo '<br>';
            echo '<br>';
            while ($idx < count($result)) {
                $col_data = $result[$idx]->$column;
                $vs = $result[$idx]->valid_start;
                $ve = $result[$idx]->valid_end;

                $idx2 = $idx + 1;

                $compressed_vs = $vs;
                $compressed_ve = $ve;
                while ($idx2 < count($result) && $result[$idx2]->$column === $result[$idx]->$column) {
                    $vs2 = $result[$idx2]->valid_start;
                    $ve2 = $result[$idx2]->valid_end;

                    $left = new DateTime(max($compressed_vs, $vs2));
                    $right = new DateTime(min($compressed_ve, $ve2));
                    $diff = $left->diff($right);
                    if ($right > $left && $diff->d > -2) {
                        $compressed_vs = min($compressed_vs, $vs2);
                        $compressed_ve = max($compressed_ve, $ve2);
                    } else {
                        break;
                    }
                    $idx2++;
                }
                array_push($compressed_result, array($column => $col_data, 'valid_start' => $compressed_vs, 'valid_end' => $compressed_ve));
                $idx=$idx2;
            }

            foreach ($compressed_result as $row) {
                echo json_encode($row) . '<br>';
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
        $result = DB::table($table)
                ->whereRaw($where)
                ->get();
        return $result;
    }

}
