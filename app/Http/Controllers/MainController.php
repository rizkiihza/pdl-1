<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                        $value = $this->reformatDate($value);
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
            // example : insert (8, Joko Widodo, Indonesia, 2014-08-17, 2019-08-17) presidens

            $table_name = $query_pieces[count($query_pieces)-1];
            preg_match('#\((.*?)\)#', $raw_query, $inserted_value);
            $value = $inserted_value[0];

            $this->insert($value, $table_name);
        }

        else if ($query_pieces[0] == "delete") {
            $table = $query_pieces[1];
            $where = $query_pieces[2];
            $result = $this->delete($table, $where);
            echo $result;
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
                    $interval =  $left->diff($right);
                    $diff = $interval->format('%r%a');
                    if ((int)$diff >= -1) {
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

    private function insert($value, $table) {
        $raw_value = substr($value, 1, strlen($value)-2);
        
        // get all the attribute from table
        $inserted_value = explode(',', $raw_value);
        $idx = 0; $where_array = [];
        $first_item = DB::table($table)->first();
        foreach($first_item as $key=>$value) {
            $first_item->$key = $inserted_value[$idx];
            if($key != 'id' && $key != 'valid_start' && $key != 'valid_end')
                $where_array[$key] = trim($inserted_value[$idx]);
            $idx++;
        }
        $inserted_value = (array) $first_item;

        // prepare where statement
        $where_array_text = ''; $idx = 0;
        foreach ($where_array as $i => $x) {
            if ($idx != 0)
                $where_array_text .= ' and ';
            $where_array_text .= $i.' = "'.$x.'"';
            $idx++;
        }

        // detect conflict value
        $valid_start = $inserted_value['valid_start'];
        $valid_end = $inserted_value['valid_end'];
        $conflicted_value = DB::table($table)
            ->select("id")
            ->whereRaw($where_array_text)
            ->whereRaw("((valid_start BETWEEN '$valid_start' AND '$valid_end') OR (valid_end BETWEEN '$valid_start' AND '$valid_end'))")
            ->count();
        if ($conflicted_value > 0) {
            echo "Error: Duplicate item with overlapping valid time!";
            return;
        }

        // insert value
        $success = DB::table($table)->insert($inserted_value);
        echo "Success: new item inserted to $table";
    }

    private function delete($table, $where){
        $sql_where = str_replace("&", " AND ", $where);
        $result = DB::table($table)
                ->whereRaw($sql_where)
                ->update(['is_deleted' => 1]);
        return $result;
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


    // helper function
    private function reformatDate($raw_date) {
        $date = new DateTime($raw_date);
        $simple_date = $date->format('Y-m-d');

        return $simple_date;
    }
}
