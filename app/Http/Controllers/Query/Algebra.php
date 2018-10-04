<?php
    namespace App\Http\Controllers\Query;

    use Illuminate\Support\Facades\DB;
    use DateTime;

    class Algebra {
            public static function projection($column, $table) {
                if ($column == "*") {
                    $result = DB::table($table)->get();
                    return $result;
                }

                else {
                    $result = DB::table($table)->orderBy($column)->orderBy('valid_start')->select($column, 'valid_start', 'valid_end')->get();

                    $idx = 0;
                    $compressed_result = array();
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

                    return $compressed_result;
                }
            }

            public static function insert($value, $table) {
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

            public static function delete($table, $where){
                $sql_where = str_replace("&", " AND ", $where);
                $result = DB::table($table)
                        ->whereRaw($sql_where)
                        ->update(['is_deleted' => 1]);
                return $result;
            }

            public static function timeslice($table, $date) {
                $result = DB::table($table)->
                    where('valid_start', '<=', $date)->
                    where('valid_end', '>=', $date)->
                    get();

                foreach ($result as $row) {
                    echo json_encode($row) . '<br>';
                }
            }

            public static function select($table, $where) {
                $sql_where = str_replace("&", " AND ", $where);
                $result = DB::table($table)
                        ->whereRaw($sql_where)
                        ->get();
                foreach ($result as $row) {
                    echo json_encode($row) . '<br>';
                }
            }

            public static function join($first_table, $second_table) {
                $firstTableName = $first_table."_name";
                $secondTableName = $second_table."_name";
                $firstTableValidStart = $first_table."_valid_start";
                $secondTableValidStart = $second_table."_valid_start";
                $firstTableValidEnd = $first_table."_valid_end";
                $secondTableValidEnd = $second_table."_valid_end";

                $result = DB::table($first_table)
                    ->join($second_table, $first_table.".country", "=", $second_table.".country")
                    ->select($first_table.".name AS ".$firstTableName,
                        $second_table.".name AS ".$secondTableName,
                        $second_table.".country",
                        $first_table.".valid_start AS ".$firstTableValidStart,
                        $second_table.".valid_start AS ".$secondTableValidStart,
                        $first_table.".valid_end AS ".$firstTableValidEnd,
                        $second_table.".valid_end AS ".$secondTableValidEnd)
                    ->get();

                foreach ($result as $row) {
                    $joinValidStart = date($row->$firstTableValidStart) > date($row->$secondTableValidStart) ?
                        $row->$firstTableValidStart : $row->$secondTableValidStart;
                    $joinValidEnd = date($row->$firstTableValidEnd) < date($row->$secondTableValidEnd) ?
                        $row->$firstTableValidEnd : $row->$secondTableValidEnd;
                    // delete unusable columns
                    unset($row->$firstTableValidStart);
                    unset($row->$secondTableValidStart);
                    unset($row->$firstTableValidEnd);
                    unset($row->$secondTableValidEnd);
                    // final check
                    if ($joinValidStart < $joinValidEnd) {
                        $row->validStart = $joinValidStart;
                        $row->validEnd = $joinValidEnd;
                    } else {
                        $row->validStart = False;
                        $row->validEnd = False;
                    }
                }

                $mergedResult = array();
                foreach ($result as $row) {
                    if ($row->validStart && $row->validEnd) {
                        array_push($mergedResult, $row);
                    }
                }

                foreach ($mergedResult as $row) {
                    echo json_encode($row) . '<br>';
                }
            }


            public static function union($column, $table1, $table2) {
                $table_1 = $this->projection($table1, $column);
                $table_2 = $this->projection($table, $column);

                foreach($table_1 as $row) {
                    echo $row . '<br>';
                }
                echo '<br><br>';
                foreach($table_2 as $row) {
                    echo $row . '<br>';
                }
            }

            // helper function
            public static function reformatDate($raw_date) {
                $date = new DateTime($raw_date);
                $simple_date = $date->format('Y-m-d');

                return $simple_date;
            }
        }