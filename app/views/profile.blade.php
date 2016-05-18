@extends('layout')

@section('content')
    <div class="container application">
        <div class="row row-eq-height">
                <div class="col-md-3">
                    <p>To be implemented: Account information</p>
                </div>
                <div class="col-md-9 main">
                    <div class="row">
                    </div>
                    <h1>Your Profile</h1>
                    {{ Form::open(array('url'=>'users/changeinformation')) }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('last_name', 'Last Name') }}
                                    {{ Form::text('last_name', Auth::user()->last_name, array('class'=>'form-control', 'placeholder'=>'last name..')) }}
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('first_name', 'First Name') }}
                                    {{ Form::text('first_name', Auth::user()->first_name, array('class'=>'form-control', 'placeholder'=>'first name..')) }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    {{ Form::label('email', 'E-Mail Address') }}
                                    {{ Form::text('email', Auth::user()->email, array('class'=>'form-control', 'placeholder'=>'email..')) }}
                                </div>
                            </div>
                            <div class="col-md-12">
                                {{ Form::label(null, 'Change password') }}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::password('new_password', array('class'=>'form-control', 'placeholder'=>'new password..')) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::password('old_password', array('class'=>'form-control', 'placeholder'=>'password confirmation..')) }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'old password..')) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            {{ Form::submit('Submit changes', array('class'=>'btn'))}}
                        </div>
                    {{ Form::close() }}
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
    <script src="{{ URL::to('scripts/landing.js') }}"></script>
@stop