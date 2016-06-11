{{ Form::open(array('url'=>'users/changeinformation')) }}
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('last_name', 'Last Name') }}
                {{ Form::text('last_name', Auth::user()->last_name, array('class'=>'form-control', 'placeholder'=>'last name..')) }}
            </div>
        </div>
         <div class="col-sm-6">
            <div class="form-group">
                {{ Form::label('first_name', 'First Name') }}
                {{ Form::text('first_name', Auth::user()->first_name, array('class'=>'form-control', 'placeholder'=>'first name..')) }}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {{ Form::label('email', 'E-Mail Address') }}
                {{ Form::text('email', Auth::user()->email, array('class'=>'form-control', 'placeholder'=>'email..')) }}
            </div>
        </div>
        <div class="col-sm-12">
            {{ Form::label(null, 'Change password') }}
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::password('new_password', array('class'=>'form-control', 'placeholder'=>'new password..')) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::password('old_password', array('class'=>'form-control', 'placeholder'=>'password confirmation..')) }}
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{ Form::password('password', array('class'=>'form-control', 'placeholder'=>'old password..')) }}
            </div>
        </div>
    </div>
    <div class="text-right">
        {{ Form::submit('Submit changes', array('class'=>'btn'))}}
    </div>
{{ Form::close() }}