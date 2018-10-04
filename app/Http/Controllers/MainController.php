<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Query\Allen;
use App\Http\Controllers\Query\Algebra;
use DateTime;

class MainController extends Controller
{

    public function index(Request $request) {
        $tables = DB::select('SHOW TABLES');
        $table_data = [];
        foreach($tables as $table) {
            if ($table->Tables_in_homestead == 'migrations' || $table->Tables_in_homestead == 'password_resets' || $table->Tables_in_homestead == 'users')
                continue;

            $rawdata = DB::table($table->Tables_in_homestead)->get();

            // modify valid_start and valid_end column so its only show the date, ignore the hour
            foreach ($rawdata as $idx => $rawdataitem) {
                foreach ($rawdataitem as $key => $value) {
                    if ($key == 'valid_start' || $key == 'valid_end') {
                        $value = Algebra::reformatDate($value);
                    }
                    $rawdata[$idx]->$key = $value;
                }
            }
            array_push($table_data, array('name'=>$table->Tables_in_homestead, 'data'=>$rawdata));
        }

        return view('welcome', ['table_data'=>$table_data]);
    }

    public function handleQuery(Request $request) {
        $raw_query = $request->query('query');
        $query_pieces = explode(" ", $raw_query);

        if ($query_pieces[0] === "select") {
            $table = $query_pieces[1];
            $where = $query_pieces[2];
            $result = Algebra::select($table, $where);
            foreach ($result as $idx => $rawdataitem) {
                foreach ($rawdataitem as $key => $value) {
                    if ($key == 'valid_start' || $key == 'valid_end') {
                        $value = Algebra::reformatDate($value);
                    }
                    $result[$idx]->$key = $value;
                }
            }
            $this->makeTable($result);
        }

        else if ($query_pieces[0] === "projection") {
            if (count($query_pieces) != 3) {
                return redirect('/');
            }

            $columns = $query_pieces[1];
            $table = $query_pieces[2];

            $result = Algebra::projection($columns, $table);
            foreach ($result as $idx => $rawdataitem) {
                foreach ($rawdataitem as $key => $value) {
                    if ($key == 'valid_start' || $key == 'valid_end') {
                        $value = Algebra::reformatDate($value);
                    }
                    $result[$idx][$key] = $value;
                }
            }
            $this->makeTable($result);
        }

        else if ($query_pieces[0] === "join") {
            $first_table = $query_pieces[1];
            $second_table = $query_pieces[2];
            $result = Algebra::join($first_table, $second_table);
            foreach ($result as $idx => $rawdataitem) {
                foreach ($rawdataitem as $key => $value) {
                    if ($key == 'valid_start' || $key == 'valid_end') {
                        $value = Algebra::reformatDate($value);
                    }
                    $result[$idx]->$key = $value;
                }
            }
            $this->makeTable($result);
        }

        else if ($query_pieces[0] === "insert") {
            // example : insert (8, Joko Widodo, Indonesia, 2014-08-17, 2019-08-17) presidens

            $table_name = $query_pieces[count($query_pieces)-1];
            preg_match('#\((.*?)\)#', $raw_query, $inserted_value);
            $value = $inserted_value[0];

            Algebra::insert($value, $table_name);
        }

        else if ($query_pieces[0] == "delete") {
            $table = $query_pieces[1];
            $where = $query_pieces[2];
            Algebra::delete($table, $where);
        }

        else if ($query_pieces[0] == "timeslice") {
            $table = $query_pieces[1];
            $date = DateTime::createFromFormat('j-M-Y', $query_pieces[2]);
            $result = Algebra::timeslice($table, $date);
            foreach ($result as $idx => $rawdataitem) {
                foreach ($rawdataitem as $key => $value) {
                    if ($key == 'valid_start' || $key == 'valid_end') {
                        $value = Algebra::reformatDate($value);
                    }
                    $result[$idx]->$key = $value;
                }
            }
            $this->makeTable($result);
        }

        else if ($query_pieces[0] == "is") {
            $allen = $query_pieces[1];
            $table = $query_pieces[2];
            $id1 = $query_pieces[3];
            $id2 = $query_pieces[4];

            // if ($allen == "before") {
            //     $result = Allen::before($table, $id1, $id2);
            //     echo "\n\n"  .  $result;
            // }
            $result = Allen::$allen($table, $id1, $id2);
            var_dump($result);
        }
    }

    private function makeTable($result) {
        if (count($result) == 0) {
            echo "Empty result";
        }
        else {
            echo '<table align="center"; style="border:1px solid black; border-collapse: collapse">';
            echo '<tr>';
            $first_row = $result[0];
            foreach($first_row as $key => $value) {
                echo '<th align="left"; style="padding:5px; border:1px solid black; border-collapse: collapse">';
                echo $key;
                echo '</th>';
            }
            echo '</tr>';
            foreach($result as $row){
                echo '<tr>';
                foreach ($row as $col){
                    echo '<td style="padding:5px; border:1px solid black; border-collapse: collapse">';
                    echo $col;
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}
