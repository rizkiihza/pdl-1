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

                <h5>Temporal Relational Algebra</h5>

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

                <h6>Set Difference</h6>
                <p>diff [first_table] [second_table]<br>
                example: diff warga_negaras presidens</p>
                <br>

                <h6>Union</h6>
                <p>union [column] [first_table] [second_table]<br>
                example: union * warga_negaras presidens</p>
                <br>

                <h6>Delete</h6>
                <p>delete [table] [where_statement]<br>
                example: delete presidens name='Sukarno'&country='Indonesia'</p>
                <br>

                <h6>Valid Timeslice</h6>
                <p>timeslice [table] [DD-MMM-YYYY]<br>
                example: timeslice presidens 20-Aug-1945</p>
                <br>

            </div>
        </div>

        <div class="row">
            <div class="col s6 offset-s3">
                <h5>Allen's Time Interval</h5>

                <h6>After</h6>
                <p>is after [table] [idx1] [idx2]<br>
                example: is after presidens 1 2</p>
                <br>

                <h6>Before</h6>
                <p>is before [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Meet</h6>
                <p>is meet [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Met By</h6>
                <p>is metBy [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Overlap</h6>
                <p>is overlap [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Overlapped By</h6>
                <p>is overlappedBy [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Start</h6>
                <p>is start [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Started By</h6>
                <p>is startedBy [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>During</h6>
                <p>is during [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Contain</h6>
                <p>is contain [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Finish</h6>
                <p>is finish [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Finished By</h6>
                <p>is finishedBy [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

                <h6>Equal</h6>
                <p>is equal [table] [idx1] [idx2]<br>
                example: is before presidens 1 2</p>
                <br>

            </div>
        </div>

    </body>
</html>