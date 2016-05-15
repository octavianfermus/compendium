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
                <li class>{{ HTML::link('/', 'Home') }}</li>
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
                 <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Profile <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Details</a></li>
                    <li role="separator" class="divider"></li>
                    <li>{{ HTML::link('users/logout', 'Logout') }}</li>
                  </ul>
                </li>
                <li><a href="notifications.html">Notifications <span class="messageCount">99+</span></a></li>
                
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact us</a></li>

              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
        <div class="container application">
            <div class="row">
                <div class="col-md-12 main">
                    <div class="boxWrapper">
                        <h1>Algorithm management</h1>
                        <p>Hello, <span class="person" id="person-me">{{Auth::user()->last_name;}} {{Auth::user()->first_name;}}</span>. Here you can search algorithms or choose to post one yourself.</p>
                    </div>
                    <ul class="nav nav-tabs" style="margin-bottom: 25px">
                        <li role="presentation" class="active"><a id="my-posts-tab" role="tab" data-toggle="tab" aria-controls="my-posts" aria-expanded="true" href="#my-posts">My posts</a></li>
                        <li role="presentation"><a id="search-algorithms-tab" role="tab" data-toggle="tab" aria-controls="search-algorithms" aria-expanded="false" href="#search-algorithms">Search</a></li>
                        <li role="presentation"><a id="post-new-tab" role="tab" data-toggle="tab" aria-controls="post-new" aria-expanded="false" href="#post-new">Post a new algorithm</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="my-posts" aria-labelledby="my-posts-tab" style="position: relative">
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
                                    <th class="text-center">Publish / Delete</th>
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
                                        <td><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)">Palindromes Algorithm</a></td>
                                        <td>Javascript</td>
                                        <td>10</td>
                                        <td>0</td>
                                        <td>100</td>
                                        <td>23</td>
                                        <td>11</th>
                                        <td><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)">Multiple Table Join Algorithm</a></td>
                                        <td>MySQL</td>
                                        <td>4</td>
                                        <td>11</td>
                                        <td>26.66</td>
                                        <td>15</td>
                                        <td>3</td>
                                        <td><a href="javascript:void(0)"><span class="glyphicon glyphicon-remove"></span></a></td>
                                    </tr>
                                    <tr>
                                        <td><a href="javascript:void(0)">Send Email Algorithm</a></td>
                                        <td>PHP</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td>0</td>
                                        <td><a href="javascript:void(0)"><span class="glyphicon glyphicon-pencil"></span></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> 
                        <div role="tabpanel" class="tab-pane fade" id="search-algorithms" aria-labelledby="search-algorithms-tab"> 
                            <div class="boxWrapper">
                                <label>Keywords</label>
                                <input type="text" class="form-control" placeholder="keywords separated by coma">
                                <label>Programming Language</label>
                                <select class="form-control">
                                    <option>All</option>
                                    <option>Javascript</option>
                                    <option>Java</option>
                                    <option>PHP</option>
                                    <option>C</option>
                                    <option>C#</option>
                                </select>
                                <label class="checkbox-inline"><input type="checkbox" value="">Positive like/dislike ratio</label>
                                <label class="checkbox-inline"><input type="checkbox" value="">Popular algorithms</label>
                                <p><a href="javascript:void(0)" data-toggle="modal" data-target="#requestModal">Can't find what you are searching for? Submit a request</a></p>
                                <div class="text-right">
                                    <button class="btn">Search</button>
                                </div>
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
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="post-new" aria-labelledby="post-new-tab"> 
                            <div class="boxWrapper">
                                <label>Algorithm name</label>
                                <input type="text" class="form-control" placeholder="Algorithm name">
                                <label>Programming Language</label>
                                <select class="form-control">
                                    <option>All</option>
                                    <option>Javascript</option>
                                    <option>Java</option>
                                    <option>PHP</option>
                                    <option>C</option>
                                    <option>C#</option>
                                </select>
                                <label>Description</label>
                                <textarea class="form-control" placeholder="Add a short description"></textarea>
                                
                                <label>Original Link (optional)</label>
                                <input type="text" class="form-control" placeholder="Original Algorithm Location">
                                <textarea class="my-code-area" rows="4" style="width: 100%; display: none"></textarea>
                                <div class="text-right" style="margin-top: 35px;">
                                    <button class="btn" id="continueToCode">Continue</button>
                                </div>
                                <div class="text-right hidden">
                                    <button class="btn">Save as template</button>
                                    <button class="btn">Publish</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Request Algorithm</h4>
              </div>
              <div class="modal-body">
                <label>Algorithm Name</label>
                <input type="text" class="form-control" placeholder="algorithm title">
                <label>Algorithm Description</label>
                <textarea class="form-control" placeholder="short algorithm description"></textarea>
                <label>Programming Language</label>
                <select class="form-control">
                    <option>All</option>
                    <option>Javascript</option>
                    <option>Java</option>
                    <option>PHP</option>
                    <option>C</option>
                    <option>C#</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Submit Request</button>
              </div>
            </div>
          </div>
        </div>
        <script src="{{ URL::to('scripts/ace/ace.js') }}"></script>
        <script src="{{ URL::to('scripts/ace/mode-ruby.js') }}"></script>
        <script src="{{ URL::to('scripts/jquery-ace.min.js') }}"></script>
        <script src="{{ URL::to('scripts/scripts.js') }}"></script>
@stop