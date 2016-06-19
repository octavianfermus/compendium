<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
    if (Auth::guest()==true) {
        return View::make('landing');
    } else {
	   return View::make('home');
    }
});

Route::get('users', function()
{
    $users = User::all();
    return View::make('users')->with('users', $users);
});
	
Route::controller('users', 'UsersController');

Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

Route::post('password/postEmailReset', 'RemindersController@postEmailReset');

Route::get('check', function() {
    return View::make('check');
});

Route::get('contact', function() {
    return View::make('contact');
});

Route::get('about', function() {
    return View::make('about');
});

Route::get('notifications', function() {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
        Session::flush();
        return Redirect::to('/')
            ->withErrors(["This account is currently banned."]);
        }
        $time = date('Y-m-d H:i:s');
        DB::update('update notifications set seen = 1, updated_at = ? where user_id = ?', array(
            $time, 
            Auth::user()->id, 
        ));
        return View::make('notifications');
    } 
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});

Route::get('posts/{id}', function ($algorithm_id) {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
        Session::flush();
        return Redirect::to('/')
            ->withErrors(["This account is currently banned."]);
        }
        $found = DB::table('algorithms')
                    ->where('id', '=', $algorithm_id)
                    ->where('template', '=', 0)
                    ->count();
        if($found==1) {
            if(Auth::check()) {
                    $found = DB::table('algorithm_views')
                            ->where('algorithm_id', '=', $algorithm_id)
                            ->where('user_id', '=', Auth::user()->id)
                            ->count();
                if($found==0) {
                    $time = date('Y-m-d H:i:s');
                    DB::insert('insert into algorithm_views (user_id, algorithm_id, created_at, updated_at) values (?, ?, ?, ?)', array(
                        Auth::user()->id, 
                        $algorithm_id,
                        $time,
                        $time)
                    );
                    $updater = DB::table('algorithm_views')
                    ->where('algorithm_id', '=', $algorithm_id)
                    ->count();
                    DB::update('update algorithms set views = ?, updated_at = ? where id = ?', array(
                        $updater,
                        $time, 
                        $algorithm_id, 
                    ));
                }
                return View::make('post');
            } 
            return View::make('404')->withErrors(["This page cannot be reached because this post doesn't exist."]);
        }
    }
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});

Route::get('profile/me', function() {
    if(Auth::check()) {
       if(Auth::user()->user_type == 0) {
        Session::flush();
        return Redirect::to('/')
            ->withErrors(["This account is currently banned."]);
        }
    return View::make('my_profile');
    }
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});
Route::get('profile/{id}', function($user_id) {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        if($user_id == Auth::user()->id) {
            return Redirect::to('profile/me');
        } else {
            $found = DB::table('users')
                ->where('id','=', $user_id)->count();
            if($found != 0) {
                return View::make('profile');
            }
            return View::make('404')->withErrors(["This page cannot be reached because the user doesn't exist."]);
        }
    }
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});

Route::post('post/searchalgorithm', function() {
    $tags = Request::input('tags');
    $language = Request::input('language');
    $ratio = Request::input('ratio');
    if($tags=="" && $language=="" && $ratio == "false") {
        $algorithms_unfiltered = DB::table('algorithms')
                                    ->where('template','=','0')
                                    ->get();
        $algorithms = array();
        $algorithms["data"]=array();
        foreach ($algorithms_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["user_id"] = $array->user_id;
            $singular["name"] = $array->name;
            $singular["language"] = $array->language;
            $singular["description"] = $array->description;
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["views"] = $array->views;
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','algorithms')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            $name = DB::select('select * from users where id = ?', array($array->user_id));
            $singular["username"] = $name[0]->last_name." ".$name[0]->first_name;
            $algorithms["data"][]=$singular;
        }
        return Response::json($algorithms);
    } else {
        $algorithms_unfiltered_all = DB::table('algorithms')
            ->where('template','=','0')
            ->get();
        $algorithms_unfiltered = array();
        $algorithms_unfiltered_ratio = array();
        $algorithms_unfiltered_language = array();
        if($ratio=="true") {
            $algorithms_unfiltered_ratio = DB::table('algorithms')
                ->where('template','=','0')
                ->where('upvotes','!<','downvotes')
                ->get();
        } else {
            $algorithms_unfiltered_ratio = DB::table('algorithms')
                ->where('template','=','0')
                ->get();
        }
        if($language!="") {
            $algorithms_unfiltered_language = DB::table('algorithms')
                ->where('template','=','0')
                ->where('language','=',$language)
                ->get();
        } else {
            $algorithms_unfiltered_language = DB::table('algorithms')
                ->where('template','=','0')
                ->get();
        }
        
        foreach ($algorithms_unfiltered_all as $array) {
            if(in_array($array,$algorithms_unfiltered_language)&&
               in_array($array,$algorithms_unfiltered_ratio)&&
               in_array($array,$algorithms_unfiltered_language)) {
                $algorithms_unfiltered[] = $array;
            }
        }
        $algorithms = array();
        $algorithms["data"]=array();
        foreach ($algorithms_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["user_id"] = $array->user_id;
            $singular["name"] = $array->name;
            $singular["language"] = $array->language;
            $singular["description"] = $array->description;
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["views"] = $array->views;
            $name = DB::select('select * from users where id = ?', array($array->user_id));
            $singular["username"] = $name[0]->last_name." ".$name[0]->first_name;
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','algorithms')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            if(strlen($tags)) {
                $tagArray = explode(",", $tags);
                $inIt = false;
                foreach($tagArray as $tag) {
                    if($inIt == false) {
                        if (
                            strpos(strtolower($singular["name"]), strtolower($tag)) !== false || 
                            strpos(strtolower($singular["description"]), strtolower($tag)) !== false ||
                            strpos(strtolower($singular["username"]), strtolower($tag)) !== false || 
                            strpos(strtolower($singular["language"]), strtolower($tag)) !== false
                        ) {
                            $algorithms["data"][]=$singular;
                            $inIt = true;
                        }
                    }
                }        
            } else {
                $algorithms["data"][]=$singular;
            }
        }
        return Response::json($algorithms);
    }
});
Route::get('post/postdata', function() {
    $algorithm_id = Request::input('id');
    $returnData = array();
    $found = DB::table('algorithms')
        ->where('id', '=', $algorithm_id)
        ->where('template', '=', 0)
        ->count();
    if($found==1) {
        $unparsedData = DB::select('select * from algorithms where id = ?', array($algorithm_id));
        $returnData["upvotes"] = $unparsedData[0]->upvotes;
        $returnData["downvotes"] = $unparsedData[0]->downvotes;
        $returnData["views"] = $unparsedData[0]->views;
        $returnData["name"] = $unparsedData[0]->name;
        $returnData["original_link"] = $unparsedData[0]->original_link;
        $returnData["content"] = $unparsedData[0]->content;
        $returnData["description"] = $unparsedData[0]->description;
        $returnData["language"] = $unparsedData[0]->language;
        $returnData["user_id"] = $unparsedData[0]->user_id;
        $returnData["request_id"] = $unparsedData[0]->request_id;
        $name = DB::select('select * from users where id = ?', array($unparsedData[0]->user_id));
        $returnData["username"] = $name[0]->last_name." ".$name[0]->first_name;
        $returnData["commendations"]["number"] = DB::table('user_commendations')
            ->where('user_id','=', $unparsedData[0]->user_id)
            ->count();
        $returnData["reported"] = DB::table('reports')
            ->where('user_id','=',Auth::user()->id)
            ->where('tbl','=','algorithms')
            ->where('reported_id','=',$algorithm_id)
            ->where('reported_user_id','=',$unparsedData[0]->user_id)
            ->count();
        if(Auth::check()) {
            $commended = DB::table('user_commendations')
                ->where('user_id','=', $unparsedData[0]->user_id)
                ->where('commendator','=', Auth::user()->id)
                ->count();
            if($commended == 1) {
                $returnData["commendations"]["commendedByYou"] = TRUE; 
            } else {
                $returnData["commendations"]["commendedByYou"] = FALSE;
            }
            if($unparsedData[0]->user_id != Auth::user()->id) {
                $returnData["commendations"]["youCantCommend"] = FALSE;
            } else {
                $returnData["commendations"]["youCantCommend"] = TRUE;
            }
        } else {
            $returnData["commendations"]["commendedByYou"] = FALSE;
            $returnData["commendations"]["youCantCommend"] = TRUE;
        }
        $comments_unfiltered = DB::table('algorithm_discussion')
            ->where('algorithm_id', '=', $algorithm_id)
            ->orderBy('created_at','desc')
            ->get();
        $comments = array();
        foreach ($comments_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["user_id"] = $array->user_id;
            $singular["text"] = $array->text;
            $singular["deleted"] = $array->deleted;
            if($singular["user_id"] == Auth::user()->id) {
                $singular["canDelete"] = true;
            } else {
                $singular["canDelete"] = false;
            }
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','algorithm_discussion')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            $singular["created_at"] = $array->created_at;
            $singular["replies"] = array(); 
            $reply_comments_unfiltered = DB::table('algorithm_discussion_replies')
                ->where('algorithm_id', '=', $algorithm_id)
                ->where('comment_id', '=', $singular["id"])
                ->get();
            foreach ($reply_comments_unfiltered as $secondaryArray) {
                $secondarySingular = array();
                $secondarySingular["id"] = $secondaryArray->id;
                $secondarySingular["user_id"] = $secondaryArray->user_id;
                $secondarySingular["text"] = $secondaryArray->text;
                $secondarySingular["deleted"] = $secondaryArray->deleted;
                if($secondarySingular["user_id"] == Auth::user()->id) {
                    $secondarySingular["canDelete"] = true;
                } else {
                    $secondarySingular["canDelete"] = false;
                }
                $secondarySingular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','algorithm_discussion_replies')
                    ->where('reported_id','=',$secondaryArray->id)
                    ->where('reported_user_id','=',$secondaryArray->user_id)
                    ->count();
                $secondarySingular["created_at"] = $secondaryArray->created_at;
                $secondarySingular["upvotes"] = $secondaryArray->upvotes;
                $secondarySingular["downvotes"] = $secondaryArray->downvotes;
                $secondaryName = DB::select('select * from users where id = ?', array($secondaryArray->user_id));
                $secondarySingular["name"] = $secondaryName[0]->last_name." ".$secondaryName[0]->first_name;
                $singular["replies"][] = $secondarySingular;
            }

            $name = DB::select('select * from users where id = ?', array($array->user_id));
            $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
            $comments[]=$singular;
        }
        $returnData["comments"]=$comments;
        $comments_unfiltered = DB::table('inline_algorithm_comments')
            ->where('algorithm_id', '=', $algorithm_id)
            ->orderBy('created_at','desc')
            ->get();
        $comments = array();
        foreach ($comments_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["line"] = $array->line;
            $singular["user_id"] = $array->user_id;
            $singular["text"] = $array->text;
            $singular["deleted"] = $array->deleted;
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["created_at"] = $array->created_at;     
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','inline_algorithm_comments')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            $name = DB::select('select * from users where id = ?', array($array->user_id));
            $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
            $comments[]=$singular;
        }
        $returnData["inline_comments"]=$comments;
        return Response::json($returnData);
    }
    return Response::json(array('data'=>$returnData));
});
Route::get('userlist', function() {
    $returnData = array();
    
    $unparsed_users = DB::table('users')
        ->get();
    foreach($unparsed_users as $array) {
        $singular = array();
        $singular["id"] = $array->id;
        $singular["last_name"]= $array->last_name;
        $singular["first_name"] = $array->first_name;
        //$singular["type"]=$array->type;
        $returnData[]=$singular;
    }
    return Response::json($returnData);
});

Route::post('profiledetails', function() {
    if(Auth::user()->user_type == 0) {
        Session::flush();
        return Redirect::to('/')
            ->withErrors(["This account is currently banned."]);
    }
    $id = Input::get('id');
    $profile_id = Request::input('profile_id');
    if($id == "me") {
        $id = Auth::user()->id;
    }
    $returnData = array();
    $returnData["requested_user_id"]=$id;
    $found = DB::table('users')
        ->where('id','=', $id)
        ->first();
    if($found) {
        $returnData["userFound"]=TRUE;
        $returnData["userData"]["firstName"]=$found->first_name;
        $returnData["userData"]["lastName"]=$found->last_name;
        $returnData["userData"]["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','users')
                ->where('reported_id','=',$id)
                ->where('reported_user_id','=',$id)
                ->count();
        $returnData["userData"]["commendations"]["number"] = DB::table('user_commendations')
            ->where('user_id','=', $id)
            ->count();
        if(Auth::check()) {
            $commended = DB::table('user_commendations')
                ->where('user_id','=', $id)
                ->where('commendator','=', Auth::user()->id)
                ->count();
            if($commended == 1) {
                $returnData["userData"]["commendations"]["commendedByYou"] = TRUE; 
            } else {
                $returnData["userData"]["commendations"]["commendedByYou"] = FALSE;
            }
            if($id != Auth::user()->id) {
                $returnData["userData"]["commendations"]["youCantCommend"] = FALSE;
            } else {
                $returnData["userData"]["commendations"]["youCantCommend"] = TRUE;
            }
        } else {
            $returnData["userData"]["commendations"]["commendedByYou"] = FALSE;
            $returnData["userData"]["commendations"]["youCantCommend"] = TRUE;
        }
        $algorithms_unfiltered = DB::table('algorithms')
            ->where('user_id', '=', $id)
            ->where('template','=',0)
            ->get();
        $algorithms = array();
        foreach ($algorithms_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["name"] = $array->name;
            $singular["language"] = $array->language;
            $singular["description"] = $array->description;
            $singular["template"] = $array->template;
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','algorithms')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            $singular["views"] = $array->views;
            $singular["comments"] = DB::table('algorithm_discussion')
                ->where('algorithm_id','=',$array->id)
                ->count();
            $algorithms[]=$singular;
        }
        $returnData["algorithms"]=$algorithms;
        
        $comments_unfiltered = DB::table('profile_discussion')->where('profile_id', '=', $id)->get();
        $comments = array();
        foreach ($comments_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["user_id"] = $array->user_id;
            $singular["text"] = $array->text;
            $singular["deleted"] = $array->deleted;
            if($singular["user_id"] == Auth::user()->id) {
                $singular["canDelete"] = true;
            } else {
                $singular["canDelete"] = false;
            }
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["reported"] = DB::table('reports')
                ->where('user_id','=',Auth::user()->id)
                ->where('tbl','=','profile_discussion')
                ->where('reported_id','=',$array->id)
                ->where('reported_user_id','=',$array->user_id)
                ->count();
            $singular["created_at"] = $array->created_at;
            $singular["replies"] = array(); 
            $reply_comments_unfiltered = DB::table('profile_discussion_replies')
                ->where('profile_id', '=', $id)
                ->where('comment_id', '=', $singular["id"])
                ->get();
            foreach ($reply_comments_unfiltered as $secondaryArray) {
                $secondarySingular = array();
                $secondarySingular["id"] = $secondaryArray->id;
                $secondarySingular["user_id"] = $secondaryArray->user_id;
                $secondarySingular["text"] = $secondaryArray->text;
                $secondarySingular["deleted"] = $secondaryArray->deleted;
                if($secondarySingular["user_id"] == Auth::user()->id) {
                    $secondarySingular["canDelete"] = true;
                } else {
                    $secondarySingular["canDelete"] = false;
                }
                $secondarySingular["created_at"] = $secondaryArray->created_at;
                $secondarySingular["upvotes"] = $secondaryArray->upvotes;
                $secondarySingular["downvotes"] = $secondaryArray->downvotes;
                $secondarySingular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','profile_discussion_replies')
                    ->where('reported_id','=',$secondaryArray->id)
                    ->where('reported_user_id','=',$secondaryArray->user_id)
                    ->count();
                $secondaryName = DB::select('select * from users where id = ?', array($secondaryArray->user_id));
                $secondarySingular["name"] = $secondaryName[0]->last_name." ".$secondaryName[0]->first_name;
                $singular["replies"][] = $secondarySingular;
            }

            $name = DB::select('select * from users where id = ?', array($array->user_id));
            $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
            $comments[]=$singular;
        }
        $returnData["comments"]=$comments;
        $statistics = array();
        $statistics["algorithm_comments"] = DB::table('algorithm_discussion') 
            ->where('user_id','=',$id)
            ->count();
        $statistics["algorithm_replies"] = DB::table('algorithm_discussion_replies') 
            ->where('user_id','=',$id)
            ->count();
        $statistics["profile_comments"] = DB::table('profile_discussion') 
            ->where('user_id','=',$id)
            ->count();
        $statistics["profile_replies"] = DB::table('profile_discussion_replies') 
            ->where('user_id','=',$id)
            ->count();
        $statistics["algorithm_likes"] = DB::table('algorithm_votes') 
            ->where('user_id','=',$id)
            ->where('vote','=',1)
            ->count();
        $statistics["algorithm_requests"] = DB::table('algorithm_requests') 
            ->where('user_id','=',$id)
            ->count();
        $statistics["given_commendations"] = DB::table('user_commendations') 
            ->where('commendator','=',$id)
            ->count();
        $returnData["statistics"]=$statistics;
    } else {
        $returnData["user_found"]=FALSE;
    }
    return Response::json($returnData);
});

Route::get('messages/{id}', function($id) {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        $found = DB::table('users')
            ->where('id','=', $id)->count();
        if($found != 0) {
            $time = date('Y-m-d H:i:s');
            DB::update('update private_messages set seen = 1, updated_at = ? where to_id = ? and from_id = ? and seen = 0', array(
                $time, 
                Auth::user()->id,
                $id
            ));
        }
        return View::make('404')->withErrors(["This page cannot be reached because the user you are trying to talk to doesn't exist."]);
    } else {
        return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
    }  
});
Route::get('messages', function() {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        return View::make('messages');
    }
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});

Route::get('groups/{id}', function($id) {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        $found = DB::table('groups')
            ->where('id','=', $id)
            ->where('visible','=',1)
            ->count();
        if($found == 1) {
            $member_me = DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('member_id','=',Auth::user()->id)
                ->where('accepted','=',1)
                ->count();
            if($member_me==1) {
                $time = date('Y-m-d H:i:s');
                DB::update('update group_members set read_last_message = 1, updated_at = ? where group_id = ? and member_id = ? and accepted = 1 and read_last_message = 0', array(
                    $time,
                    $id,
                    Auth::user()->id
                ));
                return View::make('groupchat');
            } else {
                return View::make('joingroup');
            }
        } else {
            return View::make('404')->withErrors(["This page cannot be reached because the group doesn't exist."]);
        }
    } else {
        return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
    }  
});
Route::get('groups', function() {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        return View::make('groups');
    } 
    return View::make('404')->withErrors(["This page cannot be reached. You are not logged in."]);
});