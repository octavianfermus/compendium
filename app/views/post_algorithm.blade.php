<div class="boxWrapper">
    {{ Form::open(array('url'=>'users/pushalgorithm', 'id'=>'post_algorithm_form')) }}
        {{ Form::label('algorithm_name', 'Algorithm name') }}
        {{ Form::text('algorithm_name', null, array('class'=>'form-control', 'placeholder'=>'algorithm name..')) }}
        {{ Form::label('programming_language', 'Programming Language') }}
        {{ Form::select('language', array(
            'Javascript' => 'Javascript',
            'Java' => 'Java', 
            'PHP' => 'PHP', 
            'C' => 'C',
            'C#' => 'C#'
        ), null, array('class'=>'form-control')) }}
        {{ Form::label('description', 'Description') }}
        {{ Form::textarea('algorithm_description', null, array('class'=>'form-control', 'rows'=>'2', 'placeholder'=>'add a short description..')) }}
        {{ Form::label('original_link', 'Original Link (optional)') }}
        {{ Form::text('original_link', null, array('class'=>'form-control', 'placeholder'=>'original algorithm location..')) }}
        <div id="editor">
        </div>
        {{ Form::textarea('algorithm_code', null, array('class'=>'hidden', 'placeholder'=>'add a short description..')) }}
        {{ Form::text('template', null, array('class'=>'hidden','id'=>'isItTemplate')) }}
        {{ Form::submit('Submit changes', array('class'=>'btn hidden', 'id'=>'submit_algorithm'))}}
    {{ Form::close() }}
        <div class="text-right" style="margin-top: 35px;">
            <button class="btn" id="continueToCode">Continue</button>
        </div>
        <div class="text-right hidden">
            <button class="btn" id="saveAsTemplate">Save as template</button>
            <button class="btn" id="publishAlgorithm">Publish</button>
        </div>
</div>