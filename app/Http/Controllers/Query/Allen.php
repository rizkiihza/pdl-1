<?php
    namespace App\Http\Controllers\Query;

    use Illuminate\Support\Facades\DB;
    use DateTime;

    class Allen {

        public static function before($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_end < $second_row->valid_start) ? TRUE : FALSE;
        }

        public static function after($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start > $second_row->valid_end) ? TRUE : FALSE;
        }

        public static function meet($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_end == $second_row->valid_start) ? TRUE : FALSE;
        }

        public static function metBy($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start == $second_row->valid_end) ? TRUE : FALSE;
        }

        public static function overlap($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            if ($first_row->valid_end <= $second_row->valid_start) return FALSE;
            if ($first_row->valid_end >= $second_row->valid_end) return FALSE;
            if ($first_row->valid_start >= $second_row->valid_start) return FALSE;

            return TRUE;
        }

        public static function overlappedBy($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            if ($first_row->valid_start >= $second_row->valid_end) return FALSE;
            if ($first_row->valid_start <= $second_row->valid_start) return FALSE;
            if ($first_row->valid_end <= $second_row->valid_end) return FALSE;

            return TRUE;
        }

        public static function start($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start == $second_row->valid_start && $second_row->valid_end > $first_row->valid_end) ? TRUE : FALSE;
        }

        public static function startedBy($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start == $second_row->valid_start && $first_row->valid_end > $second_row->valid_end) ? TRUE : FALSE;
        }

        public static function during($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start > $second_row->valid_start && $first_row->valid_end < $second_row->valid_end) ? TRUE : FALSE;
        }

        public static function contain($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($second_row->valid_start > $first_row->valid_start && $second_row->valid_end < $first_row->valid_end) ? TRUE : FALSE;
        }

        public static function finish($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_end > $second_row->valid_end && $first_row->valid_start > $second_row->valid_start) ? TRUE : FALSE;
        }

        public static function finishedBy($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($second_row->valid_end > $first_row->valid_end && $second_row->valid_start > $first_row->valid_start) ? TRUE : FALSE;
        }

        public static function equal($table, $idx1, $idx2) {
            $first_row = DB::table($table)->where('id','=', $idx1)->first();
            $second_row = DB::table($table)->where('id', '=', $idx2)->first();

            return ($first_row->valid_start == $second_row->valid_start && $first_row->valid_end == $second_row->valid_end) ? TRUE : FALSE;
        }

    }

