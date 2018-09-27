<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function handleQuery($query_string) {
        $query_pieces = explode(" ", $query_string);

        if ($query_pieces[0] === "select") {
            echo 1;
        }

        else if ($query_pieces[0] === "projection") {
            echo 2;
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
}
