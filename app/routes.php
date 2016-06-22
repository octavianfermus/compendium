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
	
Route::controller('users', 'UsersController');
Route::controller('post', 'PostController');
Route::controller('notifications', 'NotificationsController');
Route::controller('requests', 'RequestController');
Route::controller('reports', 'ReportController');
Route::controller('messaging', 'MessagingController');
Route::controller('administrative', 'AdminController');

Route::get('admin', function() {
    if(Auth::check() && Auth::user()->user_type > 1) {
        return View::make('admin');
    } else {
        return Redirect::to('/')
            ->withErrors(["This account does not have the required privilege."]);
    }
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
        }
        return View::make('404')->withErrors(["This page cannot be reached because this post doesn't exist or it isn't public yet."]);
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

Route::get('messages/{id}', function($id) {
    if(Auth::check()) {
        if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
        }
        $found = DB::table('users')
            ->where('id','=', $id)
            ->count();
        if($found != 0) {
            $time = date('Y-m-d H:i:s');
            DB::update('update private_messages set seen = 1, updated_at = ? where to_id = ? and from_id = ? and seen = 0', array(
                $time, 
                Auth::user()->id,
                $id
            ));
            return View::make('privatechat');
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