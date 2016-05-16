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

Route::get('check', function() {
    return View::make('check');
});

Route::get('contact', function() {
    return View::make('contact');
});

Route::get('about', function() {
    return View::make('about');
});

Route::get('admin', function() {
    return View::make('admin');
});

Route::get('profile', function() {
    return View::make('profile');
});

Route::get('notifications', function() {
    return View::make('notifications');
});