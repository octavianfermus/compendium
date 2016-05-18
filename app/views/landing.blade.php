@extends('layout')
@section('content')
    
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
                        {{ Form::open(array('url'=>'password/postEmailReset', 'class'=>'form-reset')) }}
                            <div class="form-group">
                                {{ Form::text('email', null, array('class'=>'form-control', 'id'=>'email', 'placeholder'=>'email..')) }}
                            </div>
                            <div class="text-right">
                                {{ Form::submit('Send password reset email', array('class'=>'btn'))}}
                            </div>
                        {{ Form::close() }}
                    </div> 
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Errors</h4>
                </div>
                <div class="modal-body">
                    @if(count($errors))
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script src="{{ URL::to('scripts/errorModal.js') }}"></script>
@stop