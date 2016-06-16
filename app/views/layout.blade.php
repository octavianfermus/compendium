<!DOCTYPE html>

<html lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Algorithm Compendium</title>
        <link href="{{ URL::asset('stylesheet/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('stylesheet/jquery.tagit.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('stylesheet/tagit.ui-zendesk.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('stylesheet/jquery-ui.min.css')}}" rel="stylesheet">
        <link href="{{ URL::asset('stylesheet/jquery-ui.theme.min.css')}}" rel="stylesheet">
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
        <script src="{{ URL::to('scripts/jquery-2.2.3.min.js') }}"></script>
        <script src="{{ URL::to('scripts/bootstrap.min.js') }}"></script>
        <script src="{{ URL::to('scripts/select-dropdowns.js') }}"></script>
        <script src="{{ URL::to('scripts/searchAlgorithms.js') }}"></script>
        <script src="{{ URL::to('scripts/jquery-ui.min.js') }}"></script>
        <script src="{{ URL::to('scripts/tag-it.min.js') }}"></script>
        @if(Auth::check())
            <script src="{{ URL::to('scripts/notifications.js') }}"></script>
        @endif
        <link href="{{ URL::asset('stylesheet/stylesheet.css')}}" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-default top-navbar">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    {{ HTML::link('/', 'Algorithm Compendium', array('class'=>'navbar-brand')) }}
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li class>{{ HTML::link('/', 'Home') }}</li>
                        
                        @if(Auth::check())
                        <!-- Logged in -->
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Profile <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li>{{ HTML::link('profile/me', 'Profile') }}</li>
                                <li>@if(Auth::user()->user_type!=1) {{ HTML::link('users/admin', 'Admin Page') }} @endif</li>
                                <li role="separator" class="divider"></li>
                                <li>{{ HTML::link('users/logout', 'Logout') }}</li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle seeNotifs" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Notifications <span class="messageCount hidden" id="notifCount"></span><span class="caret"></span></a>
                            <ul class="dropdown-menu notifications">
                                <li role="separator" class="divider"></li>
                                <li class="text-center">
                                    <a href="{{ URL::to('notifications') }}">See all <span id="restOfNotif"></span> notifications</a>
                                </li>
                            </ul>
                        </li>
                        <li><a href="{{ URL::to('messages') }}">Private messages <span class="messageCount hidden" id="messageNotifCount"></span></a></li>
                        <li><a href="{{ URL::to('groups') }}">Groups <span class="messageCount hidden" id="groupNotifCount"></span></a></li>
                        @endif
                        <li>{{ HTML::link('about', 'About') }}</li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
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