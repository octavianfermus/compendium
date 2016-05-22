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
    return View::make('post');
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
        return Response::json($returnData);
    }
    return Response::json(array('data'=>$returnData));
});
