<?php
    namespace App\Http\Controllers\Query;

    use Illuminate\Support\Facades\DB;
    use DateTime;

    class Allen {

        public static function before($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=',$idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            echo "hello world";
            if ($first_row->valid_end < $second_row->valid_start) {
                return 1;
            }
            else {
                return 0;
            }
        }
        public static function after($table, $idx1, $idx2) {

        }
        public static function meet($table, $idx1, $idx2) {

        }
        public static function metBy($table, $idx1, $idx2) {

        }
        public static function overlap($table, $idx1, $idx2) {

        }
        public static function overlappedBy($table, $idx1, $idx2) {

        }
        public static function start($table, $idx1, $idx2) {

        }
        public static function startedBy($table, $idx1, $idx2) {

        }
        public static function during($table, $idx1, $idx2) {

        }
        public static function contain($table, $idx1, $idx2) {

        }
        public static function finish($table, $idx1, $idx2) {

        }
        public static function finishedBy($table, $idx1, $idx2) {

        }
        public static function equal($table, $idx1, $idx2) {

        }
    }

