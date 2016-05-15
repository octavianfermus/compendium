@extends('layout')

@section('content')
    <nav class="navbar navbar-default top-navbar">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Algorithm Compendium</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-right">
            <li class><a href="landing.html">Home</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Links <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#">Lorem</a></li>
                <li><a href="#">Ipsum</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Dolor</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Hi</a></li>
              </ul>
            </li>
            <li><a href="about.html">About</a></li>
            <li><a href="contact.html">Contact us</a></li>

          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <div class="container specification-side">
        <div class="row">
            <div class="col-md-7">
                <h2>Start using Algorithm Compendium!</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            </div> 

            <div class="col-md-5">
                <ul class="nav nav-tabs">
                    <li role="presentation"><a id="register-tab" role="tab" data-toggle="tab" aria-controls="register-form" aria-expanded="true" href="#register-form">Register</a></li>
                    <li role="presentation" class="active"><a id="login-tab" role="tab" data-toggle="tab" aria-controls="login-form" aria-expanded="true" href="#login-form">Login</a></li>
                    <li role="presentation"><a id="forgotten-tab" role="tab" data-toggle="tab" aria-controls="forgotten-form" aria-expanded="true" href="#forgotten-form">Forgotten Password</a></li>
                </ul>
                <div class="tab-content"> 
                    <div role="tabpanel" class="tab-pane fade" id="register-form" aria-labelledby="register-tab">
                       <!--
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    
    
 
    
 -->
                        {{ Form::open(array('url'=>'users/create', 'class'=>'register-form')) }}
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ Form::text('last_name', null, array('class'=>'form-control', 'id'=>'lastName', 'placeholder'=>'last name..')) }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::text('first_name', null, array('class'=>'form-control', 'id'=>'firstName', 'placeholder'=>'first name..')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::text('email', null, array('class'=>'form-control', 'id'=>'email', 'placeholder'=>'email..')) }}
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        {{ Form::password('password', array('class'=>'form-control', 'id'=>'password', 'placeholder'=>'password..')) }}
                                    </div>
                                    <div class="col-sm-6">
                                        {{ Form::password('password_confirmation', array('class'=>'form-control', 'id'=>'password_confirmation', 'placeholder'=>'repeat password..')) }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                {{ Form::submit('Register', array('class'=>'btn'))}}
                            </div>
                        {{ Form::close() }}
                    </div> 
                    <div role="tabpanel" class="tab-pane fade active in" id="login-form" aria-labelledby="login-tab"> 
                        {{ Form::open(array('url'=>'users/signin', 'class'=>'form-signin')) }}
                            <div class="form-group">
                                {{ Form::text('email', null, array('class'=>'form-control', 'id'=>'email', 'placeholder'=>'email..')) }}
                            </div>

                            <div class="form-group">
                                {{ Form::password('password', array('class'=>'form-control', 'id'=>'password', 'placeholder'=>'password..')) }}
                            </div>

                            <div class="text-right">
                                {{ Form::submit('Login', array('class'=>'btn'))}}
                            </div>
                        {{ Form::close() }}
                    </div> 
                    <div role="tabpanel" class="tab-pane fade" id="forgotten-form" aria-labelledby="forgotten-tab">  
                        <form role="form" action="landing.html">

                            <div class="form-group">
                                <input type="password" class="form-control" id="password" placeholder="password..">
                            </div>
                             <div class="text-right">
                                <button type="submit" class="btn">Send password reset email</button>
                            </div>
                        </form>
                    </div> 
                </div>

            </div>
        </div>
    </div>
    <div class="container footer-side">
        <div class="row">
            <div class="col-md-12">
                <p>University of Alexandru Ioan Cuza</p>
                <p>Fermuș V. Vasile-Octavian</p>
            </div>

        </div>
    </div>
@stop