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
                $inserted_value = explode(',', $raw_value);

                // check inserted valid time
                if (strtotime($inserted_value[sizeof($inserted_value)-1]) < strtotime($inserted_value[sizeof($inserted_value)-2])) {
                    echo "Error: Wrong valid time!";
                    return;
                }

                // get all the attribute from table
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
                echo "Success : item deleted";
                return $result;
            }

            public static function timeslice($table, $date) {
                $result = DB::table($table)->
                    where('valid_start', '<=', $date)->
                    where('valid_end', '>=', $date)->
                    get();

                return $result;
                // foreach ($result as $row) {
                //     echo json_encode($row) . '<br>';
                // }
            }

            public static function select($table, $where) {
                $sql_where = str_replace("&", " AND ", $where);
                $result = DB::table($table)
                        ->whereRaw($sql_where)
                        ->get();
                // foreach ($result as $row) {
                //     echo json_encode($row) . '<br>';
                // }
                return $result;
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
                return $mergedResult;

                // foreach ($mergedResult as $row) {
                //     echo json_encode($row) . '<br>';
                // }
            }


            public static function union($column, $table1, $table2) {
                $all = FALSE;
                if ($column == '*') {
                    $table_1 = DB::table($table1)->get();
                    $table_2 = DB::table($table2)->get();
                    $all = TRUE;
                }
                else {
                    $table_1 = Algebra::projection($column, $table1);
                    $table_2 = Algebra::projection($column, $table2);
                }

                $total_result = array_fill(0, count($table_1), NULL);
                $used = array_fill(0, count($table_2), FALSE);

                foreach ($table_1 as $key1 => $row1) {
                    if ($all == FALSE) {
                        $total_result[$key1] = $row1;
                    } else {
                        $total_result[$key1] = array('name' => $row1->name, 'country' => $row1->country,
                                                            'valid_start' => $row1->valid_start, 'valid_end' => $row1->valid_end);
                    }
                    foreach ($table_2 as $key2 => $row2) {
                        if ($all == FALSE && $row1[$column] == $row2[$column] && $used[$key2] == FALSE) {
                            $vs1 = $row1['valid_start'];
                            $ve1 = $row1['valid_end'];

                            $vs2 = $row2['valid_start'];
                            $ve2 = $row2['valid_end'];

                            $left = new DateTime(max($vs1, $vs2));
                            $right = new DateTime(min($ve1, $ve2));
                            $interval =  $left->diff($right);
                            $diff = $interval->format('%r%a');
                            if ((int)$diff >= -1) {
                                $total_result[$key1] = array($column => $row1[$column],
                                                            'valid_start' => min($vs1, $vs2), 'valid_end' => max($ve1, $ve2));
                                $used[$key2] = TRUE;
                            }
                        }
                        else if($all == TRUE && $row1->name == $row2->name && $row1->country == $row2->country && $used[$key2] == FALSE) {
                            $vs1 = $row1->valid_start;
                            $ve1 = $row1->valid_end;

                            $vs2 = $row2->valid_start;
                            $ve2 = $row2->valid_end;

                            $left = new DateTime(max($vs1, $vs2));
                            $right = new DateTime(min($ve1, $ve2));
                            $interval =  $left->diff($right);
                            $diff = $interval->format('%r%a');
                            if ((int)$diff >= -1) {
                                $total_result[$key1] = array('name' => $row1->name, 'country' => $row1->country,
                                                            'valid_start' => min($vs1, $vs2), 'valid_end' => max($ve1, $ve2));
                                $used[$key2] = TRUE;
                            }
                        }
                    }
                }
                foreach ($table_2 as $key2 => $row2) {
                    if ($all) {
                        if ($used[$key2] == FALSE) {
                            array_push($total_result, array('name' => $row2->name, 'country' => $row2->country,
                                                        'valid_start' => $row2->valid_start, 'valid_end' => $row2->valid_end));
                        }
                    } else {
                        if ($used[$key2] == FALSE) {
                            array_push($total_result, $row2);
                        }
                    }
                }

                return $total_result;
            }

            public static function setDifference($table1, $table2) {
                $result1 = DB::table($table1)->get();
                $result2 = DB::table($table2)->get();

                $differenceResult = array();
                $length = count($result2);
                foreach ($result1 as $row1) {
                    $idx = 0;
                    $found = False;
                    while (!$found && $idx < $length) {
                        if ($row1->name == $result2[$idx]->name && 
                            $row1->country == $result2[$idx]->country) {
                            $found = True;
                        } else {
                            $idx++;
                        }
                    }
                    if (!$found) {
                        array_push($differenceResult, $row1);
                    } else {
                        // count the remaining valid time
                        $start1 = date($row1->valid_start);
                        $start2 = date($result2[$idx]->valid_start);
                        $end1 = date($row1->valid_end);
                        $end2 = date($result2[$idx]->valid_end);
                        if ($start1 < $start2 && $start2 < $end1) {
                            $remainderData1 = clone($row1);
                            $remainderData1->valid_end = $start2;
                            array_push($differenceResult, $remainderData1);
                        }
                        if ($start1 < $end2 && $end1 > $end2) {
                            $remainderData2 = clone($row1);
                            $remainderData2->valid_start = $end2;
                            array_push($differenceResult, $remainderData2);
                        }
                    }
                }
                return $differenceResult;
            }

            // helper function
            public static function reformatDate($raw_date) {
                $date = new DateTime($raw_date);
                $simple_date = $date->format('Y-m-d');

                return $simple_date;
            }
        }