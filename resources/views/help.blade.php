<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="/css/materialize.css">

    </head>
    <body>

        <div class="row center">
            <h3>Query Syntax</h3>
        </div>

        <div class="row">
            <div class="col s6 offset-s3">
                <hr>
                <p>To access our database, please use this query language</p>
                <br>

                <h6>Projection</h6>
                <p>projection [column] [table] <br>
                example: projection name warga_negaras</p>
                <br>

                <h6>Insert</h6>
                <p>insert [value] [table] <br>
                example: insert (8, Joko Widodo, Indonesia, 2014-08-17, 2019-08-17) presidens</p>
                <br>

                <h6>Select</h6>
                <p>select [table] [where]<br>
                example: select presidens country='Indonesia'&name='Soekarno'</p>
                <br>

                <h6>Join</h6>
                <p>join [first_table] [second_table]<br>
                example: join presidens warga_negaras</p>
                <br>

            </div>
        </div>

    </body>
</html>