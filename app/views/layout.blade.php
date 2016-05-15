<!DOCTYPE html>

<html lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Algorithm Compendium</title>
        <link href="{{ URL::asset('stylesheet/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('stylesheet/stylesheet.css')}}" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
        <script src="{{ URL::to('scripts/jquery-2.2.3.min.js') }}"></script>
        <script src="{{ URL::to('scripts/bootstrap.min.js') }}"></script>
    </head>
    <body>
        @yield('content')
        <div class="container footer-side">
            <div class="row">
                <div class="col-md-12">
                    <p>University of Alexandru Ioan Cuza</p>
                    <p>Fermuș V. Vasile-Octavian</p>
                </div>
            </div>
        </div>
    </body>
</html>