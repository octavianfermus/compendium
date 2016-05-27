<div class="boxWrapper">
    {{ Form::open(array('url'=>'posts/searchalgorithm', 'id'=>'search_algorithms_form')) }}
        {{ Form::label('keywords', 'Keywords') }}
        {{ Form::text('keywords', null, array('class'=>'form-control', 'placeholder'=>'keywords separated by coma..')) }}
        {{ Form::label('programming_language', 'Programming Language') }}
        {{ Form::select('language', array(
            'All' => 'All',
            'Javascript' => 'Javascript',
            'Java' => 'Java', 
            'PHP' => 'PHP', 
            'C' => 'C',
            'C#' => 'C#'
        ), null, array('class'=>'form-control')) }}
        <label class="checkbox-inline">{{ Form::checkbox('ratio', 'positive') }}Positive like/dislike ratio</label>
        <p><a href="javascript:void(0)" data-toggle="modal" data-target="#requestModal">Can't find what you are searching for? Submit a request</a></p>
        <div class="text-right">
        {{ Form::submit('Search', array('class'=>'btn', 'id'=>'submit'))}}
        </div>
    {{ Form::close() }}
    
</div>
<div style="position: relative; margin-top: 25px">
    <a href="javascript:void(0)" class="switcher" id="myPostsSwitcher">Switch View Mode</a>
    <div class="postedAlgorithms">
        <div class="postedAlgorithm">
            <h2><a target="_blank" href="algorithm.html?id=23124">Prime Number Algorithm</a> (<span>Language</span>: Java)</h2>
            <p><span>Ratings</span>: 52 upvotes, 6 downvotes with an aproval of 92.85%</p>
            <p>125 views, 23 comments</p>
            <p><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span> Delete</a></p>
        </div>
        <div class="postedAlgorithm">
            <h2><a target="_blank" href="algorithm.html?id=23124">Palindromes Algorithm</a> (<span>Language</span>: Javascript)</h2>
            <p><span>Ratings</span>: 4 upvotes, 11 downvotes with an aproval of 26.66%</p>
            <p>15 views, 3 comments</p>
            <p><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span> Delete</a></p>
        </div>
        <div class="postedAlgorithm">
            <h2><a target="_blank" href="algorithm.html?id=23124">Multiple Table Join Algorithm</a> (<span>Language</span>: MySQL)</h2>
            <p><span>Ratings</span>: 52 upvotes, 6 downvotes with an aproval of 92.85%</p>
            <p>125 views, 23 comments</p>
            <p><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span> Delete</a></p>
        </div>
        <div class="postedAlgorithm">
            <h2><a target="_blank" href="algorithm.html?id=23124">Send Email Algorithm</a> (<span>Language</span>: PHP)</h2>
            <p><span>Ratings</span>: 0 upvotes, 0 downvotes with an aproval of 0%</p>
            <p>0 views, 0 comments</p>
            <p><a href="javascript:void(0)"><span class="glyphicon glyphicon-pencil"></span> Publish</a></p>
        </div>
    </div>
    <table class="hidden">
        <thead>
            <th>Name</th>
            <th>Language</th>
            <th>Upvotes</th>
            <th>Downvotes</th>
            <th>Approval</th>
            <th>Views</th>
            <th>Comments</th>
            <th class="text-center">Publisher</th>
        </thead>
        <tbody>
            <tr>
                <td><a href="javascript:void(0)">Prime Number Algorithm</a></td>
                <td>Java</td>
                <td>52</td>
                <td>6</td>
                <td>92.85</td>
                <td>125</td>
                <td>23</th>
                <td><a href="javascript:void(0)">John</a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0)">Palindromes Algorithm</a></td>
                <td>Javascript</td>
                <td>10</td>
                <td>0</td>
                <td>100</td>
                <td>23</td>
                <td>11</th>
                <td><a href="javascript:void(0)">Kripke</a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0)">Multiple Table Join Algorithm</a></td>
                <td>MySQL</td>
                <td>4</td>
                <td>11</td>
                <td>26.66</td>
                <td>15</td>
                <td>3</td>
                <td><a href="javascript:void(0)">Ryan Cooper</a></td>
            </tr>
            <tr>
                <td><a href="javascript:void(0)">Send Email Algorithm</a></td>
                <td>PHP</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td><a href="javascript:void(0)">Harper</a></td>
            </tr>
        </tbody>
    </table>
</div>

<script src="{{ URL::to('scripts/jquery-ui.min.js') }}"></script>
<script src="{{ URL::to('scripts/tag-it.min.js') }}"></script>