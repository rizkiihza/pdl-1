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


        <div class="container">

            <div class="row center">
                <h3>Valid-Time-based Temporal Database</h3>
            </div>

            <div class="row">
                <div class="col s6 offset-s3">
                    <p>Input your query:</p>
                    <form method="get" action="/query" >
                        <textarea rows="5" cols="75" name="query"></textarea>
                        <br/>
                        <input type="submit" value="Process!">
                    </form>
                </div>
            </div>

            <div class="row center">
                <div class="col s6 offset-s3">
                    <small>Read <a href="/help">here</a> to see our query syntax. <br>Please use correct syntax here, we don't spend much effort to validate your query :')</small>
                </div>
            </div>
            
            <br>
            <br>
            <hr>

            <div class="row">
                <p>Here are the available tables in the database:</p>
                @foreach ($table_data as $table)
                <div class="col s6">
                    <b>{{ $table['name'] }}</b>
                    <table style="border: 1px solid black">
                        <tr>
                            @foreach ($table['data'][0] as $key => $value)
                            <th>{{ $key }}</th>
                            @endforeach
                        </tr>
                        @foreach ($table['data'] as $data)
                        <tr>
                            @foreach ($data as $key => $value)
                            <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </body>
</html>
