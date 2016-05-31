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
    return View::make('notifications');
});

Route::get('posts/{id}', function ($postId) {
    $found = DB::table('algorithms')
                ->where('id', '=', $postId)
                ->where('template', '=', 0)
                ->count();
    if($found==1) {
        return View::make('post');
    } 
    return View::make('404');
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
    $algorithmId = Request::input('id');
    $returnData = array();
    $found = DB::table('algorithms')
                ->where('id', '=', $algorithmId)
                ->where('template', '=', 0)
                ->count();
    if($found==1) {
        $unparsedData = DB::select('select * from algorithms where id = ?', array($algorithmId));
        $returnData["upvotes"] = $unparsedData[0]->upvotes;
        $returnData["downvotes"] = $unparsedData[0]->downvotes;
        $returnData["views"] = $unparsedData[0]->views;
        $returnData["name"] = $unparsedData[0]->name;
        $returnData["original_link"] = $unparsedData[0]->original_link;
        $returnData["content"] = $unparsedData[0]->content;
        $returnData["description"] = $unparsedData[0]->description;
        $returnData["language"] = $unparsedData[0]->language;
        $returnData["user_id"] = $unparsedData[0]->user_id;
        $name = DB::select('select * from users where id = ?', array($unparsedData[0]->user_id));
        $returnData["username"] = $name[0]->last_name." ".$name[0]->first_name;
        return Response::json($returnData);
    }
    return Response::json(array('data'=>$returnData));
});