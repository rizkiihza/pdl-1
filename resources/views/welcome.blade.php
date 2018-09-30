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
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
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
                        <input type="submit" value="Search!">
                    </form>
                </div>
            </div>

            <div class="row center">
                <div class="col s6 offset-s3">
                    <small>Please see <a href="#">here</a> to see our query syntax.</small>
                </div>
            </div>
            
            <br>
            <br>
            <hr>

            <div class="row">
                <p>Here are the available tables in the database:</p>
                @foreach ($table_data as $table)
                <div class="col s6">
                    <h6>table name: {{ $table['name'] }}</h6>
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
