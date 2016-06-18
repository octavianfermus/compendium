<?php

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UsersController extends BaseController implements RemindableInterface {
    use RemindableTrait;
    public function getRegister() {
        return View::make('landing');
    }
    public function getLogin() {
        return View::make('landing');
    }
    public function getEditalgorithm($algorithmId) {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $found = DB::table('algorithms')
                ->where('user_id', '=', Auth::user()->id)
                ->where('id', '=', $algorithmId)
                ->where('template', '=', 1)
                ->count();
            if($found==1) {
                return View::make('edit');
            }
        }
    }
    public function getLogout() {
        Session::flush();
        return Redirect::to('/');
    }
    public function getAdmin() {
        return View::make('admin');
    }
    public function getPostedalgorithms() {
        $algorithms_unfiltered = DB::table('algorithms')->where('user_id', '=', Auth::user()->id)->get();
        $algorithms = array();
        $algorithms["data"]=array();
        foreach ($algorithms_unfiltered as $array) {
            $singular = array();
            $singular["id"] = $array->id;
            $singular["name"] = $array->name;
            $singular["language"] = $array->language;
            $singular["description"] = $array->description;
            $singular["template"] = $array->template;
            $singular["upvotes"] = $array->upvotes;
            $singular["downvotes"] = $array->downvotes;
            $singular["views"] = $array->views;
            $singular["comments"] = DB::table('algorithm_discussion')
                ->where('algorithm_id','=',$array->id)
                ->count();
            $algorithms["data"][]=$singular;
        }
        return Response::json($algorithms);
    }
    public function postCreate() {
        $validator = Validator::make(Input::all(), User::$rules);
        
        if ($validator->passes()) {
            $user = new User;
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');
            $user->user_type = 1;
            $user->password = Hash::make(Input::get('password'));
            $user->save();
            if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
                return Redirect::to('/');
            }
        } else {
            //Failure
            return Redirect::to('/')->withErrors($validator)->withInput();
        }
    }
    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
            return Redirect::to('/');
        } else {
            //Failure
            return Redirect::to('/')
                ->withErrors(["Your email/password combination is invalid."])
                ->withInput();
        }     
    }
    
    public function postChangeinformation() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $errors = array();
            $updates = array();
            $first_name = Input::get('first_name');
            $last_name = Input::get('last_name');
            $email = Input::get('email');
            $password = Input::get('password');
            $old_password = Input::get('old_password');
            $new_password = Input::get('new_password');
            if($first_name != Auth::user()->first_name) {
                if(strlen($first_name) > 1) {
                    $updates['first_name'] = $first_name;
                } else {
                    $errors[] = 'The first name must be at least 2 characters.';
                }
            }
            if($last_name != Auth::user()->last_name) {
                if(strlen($last_name) > 1) {
                    $updates['last_name'] = $last_name;
                } else {
                    $errors[] = 'The last name must be at least 2 characters.';
                }
            }
            if($email != Auth::user()->email) {
                if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $updates['email'] = $email;
                    $emailcount = DB::table('users')->where('email', $email)->count();
                    if($emailcount == 0) {
                        $updates['email'] = $email;
                    } else {
                        $errors[] = 'The given email is already in use.';
                    }
                } else {
                    $errors[] = 'The given email is invalid.';
                }
            }
            if(strlen($new_password)!=0) {
                if(strlen($new_password)>5 && strlen($new_password)<13) {
                    $strings = array('AbCd1zyZ9', 'foo!#$bar');
                    if(ctype_alnum($new_password)) {
                        if($new_password == $old_password) {
                            if(Hash::check($password, Auth::user()->password)) {
                                $updates['password'] = Hash::make($new_password);
                            } else {
                                $errors[] = 'The old password does not match.';
                            }
                        } else {
                            $errors[] = 'The password confirmation does not match.';
                        }
                    } else {
                        $errors[] = 'The password may only contain letters and numbers.';
                    }
                } else {
                    $errors[] = 'The password must be between 6 and 12 characters.';
                }
            
            }
            if(count($errors)!=0 || count($updates)!=0) {
                if(count($errors)>0) {
                    return Redirect::to('profile/me')->withErrors($errors)->withInput();
                } else {
                    DB::table('users')
                        ->where('id', Auth::user()->id)
                        ->update($updates);
                    return Redirect::to('profile/me');
                }
            } else {
                return Redirect::to('profile/me');
            }
        } else {
            return Redirect::to('/');
        }
    }
    
    public function putDeletealgorithm() {
        $algorithmId = Request::input('data.id');
        $found = DB::table('algorithms')
            ->where('id', '=', $algorithmId)
            ->where('user_id', '=', Auth::user()->id)
            ->count();
        if($found==1) {
            DB::delete('delete from algorithms where id = ?', array($algorithmId));
            return Response::json(array('state' => 'success', 'message'=>'Algorithm successfuly deleted.'));
        }
        return Response::json(array('state' => 'failure', 'message'=>'Algorithm not found.'));
    }    
    public function putVoterequest() {
        $request_id = Request::input('data.id');
        $time = date('Y-m-d H:i:s');
        
        $found = DB::table('algorithm_request_votes')
                    ->where('request_id', '=', $request_id)
                    ->where('user_id', '=', Auth::user()->id)
                    ->count();
        
        if($found==1) {
            DB::delete('delete from algorithm_request_votes where request_id = ? and user_id = ?', array($request_id, Auth::user()->id));
            $updater = DB::table('algorithm_request_votes')
                ->where('request_id', '=', $request_id)
                ->count();
            DB::update('update algorithm_requests set upvotes = ?, updated_at = ? where id = ?', array(
            $updater,
            $time, 
            $request_id, 
            ));
            return Response::json(array('state' => 'success', 'message'=>'Request successfuly downvoted.'));
        } else {
            
            DB::insert('insert into algorithm_request_votes (user_id, request_id, created_at, updated_at) values (?, ?, ?, ?)', array(
                Auth::user()->id, 
                $request_id,
                $time,
                $time)
            );
            $updater = DB::table('algorithm_request_votes')
                ->where('request_id', '=', $request_id)
                ->count();
            DB::update('update algorithm_requests set upvotes = ?, updated_at = ? where id = ?', array(
            $updater,
            $time, 
            $request_id, 
            ));
            return Response::json(array('state' => 'success', 'message'=>'Request successfully upvoted.'));
        }
        
        return Response::json(array('state' => 'failure', 'message'=>'Algorithm not found.'));
       
    }
    public function postPushalgorithm() {
        $time = date('Y-m-d H:i:s');
        DB::insert('insert into algorithms (user_id, name, description, language, original_link, template, content, request_id, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
            Auth::user()->id, 
            Input::get('algorithm_name'), 
            Input::get('algorithm_description'), 
            Input::get('language'), 
            Input::get('original_link'), 
            Input::get('template'), 
            Input::get('algorithm_code'),
            Input::get('byrequest'),
            $time,
            $time)
        );
        $send_to_users = DB::table('algorithm_requests')
            ->where('id', '=', Input::get('byrequest'))
            ->where('user_id', '!=', Auth::user()->id)
            ->get();
        
        $algorithm_id = DB::table('algorithms')
            ->where('user_id', '=', Auth::user()->id)
            ->where('request_id', '=', Input::get('byrequest'))
            ->where('created_at', '=', $time)
            ->get();
        if(Input::get('template') == 0) {
            foreach ($send_to_users as $array) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $array->user_id, 
                    Auth::user()->id,
                    '/posts/'.$algorithm_id[0]->id,
                    "Request answered!",
                    "created an algorithm based on your request.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
            }
        }
        return Redirect::to('/')->withErrors(['Algorithm successfully added.']);
    }
    public function postEditalgorithm() {
        $time = date('Y-m-d H:i:s');
        DB::update('update algorithms set name = ?, language = ?, description = ?, template = ?, original_link = ?, content = ?, request_id = ?, updated_at = ? where id = ?', array(
            Input::get('algorithm_name'), 
            Input::get('language'), 
            Input::get('algorithm_description'), 
            Input::get('template'), 
            Input::get('original_link'), 
            Input::get('algorithm_code'),
            Input::get('byrequest'),
            $time,
            Input::get('algorithm_id')));
        if(Input::get('template') == 0) {
            
            $send_to_users = DB::table('algorithm_requests')
                ->where('id', '=', Input::get('byrequest'))
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $array->user_id, 
                    Auth::user()->id,
                    '/posts/'.Input::get('algorithm_id'),
                    "Request answered!",
                    "created an algorithm based on your request.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
            }
            return Redirect::to('/')->withErrors("Algorithm successfully published.");
        } else {
            return Redirect::to('/users/editalgorithm/'.Input::get('algorithm_id'))->withErrors("Algorithm successfully updated.");
        }
    }
    public function postSubmitrequest() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $algorithm_name = Input::get('algorithm_name');
            $algorithm_description = Input::get('algorithm_description');
            $language = Input::get('language');
            if($algorithm_name =="" || $algorithm_description =="" || $language =="") {
                return Redirect::to('/')->withErrors("All request fields must be completed.")->withInput();
            }
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into algorithm_requests (user_id, name, description, language, upvotes,created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $algorithm_name, 
                $algorithm_description, 
                $language,
                1,
                $time,
                $time)
            );
            
            $algorithm_request_id = DB::table('algorithm_requests')
                ->where('user_id', '=', Auth::user()->id)
                ->where('name', '=', $algorithm_name)
                ->where('description', '=', $algorithm_description)
                ->where('created_at', '=', $time)
                ->where('language', '=', $language)->first();
              
            DB::insert('insert into algorithm_request_votes (user_id, request_id, created_at, updated_at) values (?, ?, ?, ?)', array(
                Auth::user()->id, 
                $algorithm_request_id->id,
                $time,
                $time)
            );
            return Redirect::to('/')->withErrors('Algorithm request successfuly submitted.');
        } else {
            return Redirect::to('404');
        }
        
    }
    public function putPublishalgorithm() {
        $algorithmId = Request::input('data.id');
        $found = DB::table('algorithms')
                    ->where('id', '=', $algorithmId)
                    ->where('user_id', '=', Auth::user()->id)
                    ->where('template', '=', 1)
                    ->where('content', '!=', "")
                    ->count();
        if($found==1) {
            $time = date('Y-m-d H:i:s');
            DB::update('update algorithms set template = 0, updated_at = ? where user_id = ? and id = ?', array($time, Auth::user()->id, $algorithmId));
            $request_id = DB::select('select * from algorithms where user_id = ? and id = ? and template = 0 and updated_at = ?', array(Auth::user()->id, $algorithmId, $time));
            $request_id = $request_id[0]->request_id;
            $send_to_users = DB::table('algorithm_requests')
                ->where('id', '=', $request_id)
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $array->user_id, 
                    Auth::user()->id,
                    '/posts/'.$algorithmId,
                    "Request answered!",
                    "created an algorithm based on your request.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
               
            }
            return Response::json(array('state' => 'success', 'message'=>'Algorithm successfuly published.'));
        }
        return Response::json(array('state' => 'failure', 'message'=>'Algorithm was not found or doesn\'t have any content. Click on the algorithm title to add content before publishing.'));
    }
    
    public function postVotealgorithm() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('algorithm_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('vote','=',$vote)
                    ->count();
            
            if($found == 1) {
                DB::table('algorithm_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('algorithm_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('algorithm_votes')
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update algorithms set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $algorithm_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('algorithm_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->count();        
            if($found==0) {
                DB::insert('insert into algorithm_votes (user_id, algorithm_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $algorithm_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update algorithm_votes set vote = ?, updated_at = ? where user_id = ? and algorithm_id = ?', array($vote, $time,  Auth::user()->id, $algorithm_id));
            }
            
            $downvotes = DB::table('algorithm_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('algorithm_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update algorithms set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $algorithm_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote an algorithm.'));
    }
    public function postDiscussalgorithm() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('id');
            $comment = Request::input('comment');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into algorithm_discussion (user_id, algorithm_id, text, deleted, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $algorithm_id,
                $comment,
                FALSE,
                $time,
                $time)
            );
            
            $notification_user_id = DB::table('algorithms')->where('id', $algorithm_id)->first();
            $notification_user_id = $notification_user_id->user_id;
            $comment_id = DB::table('algorithm_discussion')
                ->where('algorithm_id', $algorithm_id)
                ->where('created_at', $time)
                ->where('user_id', Auth::user()->id)
                ->first();
            if($notification_user_id!=Auth::user()->id) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $notification_user_id, 
                    Auth::user()->id,
                    '/posts/'.$algorithm_id,
                    "New comment!",
                    "discussed an algorithm you submitted.",
                    $comment,
                    FALSE,
                    $comment_id->id,
                    $time,
                    $time)
                );
            }
            $send_to_users = DB::table('algorithm_discussion')
                ->where('algorithm_id', '=', $algorithm_id)
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                if($array->user_id != $notification_user_id) { 
                    DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                        $array->user_id, 
                        Auth::user()->id,
                        '/posts/'.$algorithm_id,
                        "New comment!",
                        "also discussed an algorithm you commented on.",
                        $comment,
                        FALSE,
                        $comment_id->id,
                        $time,
                        $time)
                    ); 
                }
            }
            $comments_unfiltered = DB::table('algorithm_discussion')
                ->where('algorithm_id', '=', $algorithm_id)
                ->orderBy('created_at', 'desc')
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
                $singular["created_at"] = $array->created_at;
                $singular["replies"] = array(); 
                $singular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','algorithm_discussion')
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
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
                    $singular["reported"] = DB::table('reports')
                        ->where('user_id','=',Auth::user()->id)
                        ->where('tbl','=','algorithm_discussion_replies')
                        ->where('reported_id','=',$array->id)
                        ->where('reported_user_id','=',$array->user_id)
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
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to comment.'));
    }
    public function postDiscussprofile() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $profile_id = Request::input('id');
            if($profile_id == "me") {
                $profile_id = Auth::user()->id;
            }
            $comment = Request::input('comment');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into profile_discussion (user_id, profile_id, text, deleted, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $profile_id,
                $comment,
                FALSE,
                $time,
                $time)
            );

            $comment_id = DB::table('profile_discussion')
                ->where('profile_id', $profile_id)
                ->where('created_at', $time)
                ->where('user_id', Auth::user()->id)
                ->first();
            if($profile_id!=Auth::user()->id) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $profile_id, 
                    Auth::user()->id,
                    '/profile/'.$profile_id,
                    "New comment!",
                    "commented your profile.",
                    $comment,
                    FALSE,
                    $comment_id->id,
                    $time,
                    $time)
                );
            }
            
            $comments_unfiltered = DB::table('profile_discussion')
                ->where('profile_id', '=', $profile_id)
                ->orderBy('created_at', 'desc')
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
                $singular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','profile_discussion')
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
                $singular["upvotes"] = $array->upvotes;
                $singular["downvotes"] = $array->downvotes;
                $singular["created_at"] = $array->created_at;
                $singular["replies"] = array(); 
                $reply_comments_unfiltered = DB::table('profile_discussion_replies')
                    ->where('profile_id', '=', $profile_id)
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
                        ->where('tbl','=','profile_discussion_replies')
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
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to comment.'));
    }
    public function postCommentline() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('id');
            $line = Request::input('line');
            $comment = Request::input('comment');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into inline_algorithm_comments (user_id, algorithm_id, line, text, deleted, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $algorithm_id,
                $line,
                $comment,
                FALSE,
                $time,
                $time)
            );
            
            $notification_user_id = DB::table('algorithms')->where('id', $algorithm_id)->first();
            $notification_user_id = $notification_user_id->user_id;
            if($notification_user_id!=Auth::user()->id) {
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $notification_user_id, 
                    Auth::user()->id,
                    '/posts/'.$algorithm_id,
                    "New line comment!",
                    "commented an algorithm you submitted on line ".($line+1).".",
                    $comment,
                    FALSE,
                    $line,
                    $time,
                    $time)
                );
            }
            $send_to_users = DB::table('inline_algorithm_comments')
                ->where('algorithm_id', '=', $algorithm_id)
                ->where('line', '=', $line)
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                
                if($array->user_id != $notification_user_id) { 
                    DB::insert('insert into notifications (user_id, who_said, url, title, text, seen, what_was_said, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                        $array->user_id, 
                        Auth::user()->id,
                        '/posts/'.$algorithm_id,
                        "New line comment!",
                        "commented an algorithm line you also commented on (line ".($line+1).").",
                        $comment,
                        FALSE,
                        $line,
                        $time,
                        $time)
                    );
                    
                }
            }
            $comments_unfiltered = DB::table('inline_algorithm_comments')
                ->where('algorithm_id', '=', $algorithm_id)
                ->orderBy('created_at', 'desc')
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
                $singular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','inline_algorithm_comments')
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
                $singular["downvotes"] = $array->downvotes;
                $singular["created_at"] = $array->created_at;     
                $name = DB::select('select * from users where id = ?', array($array->user_id));
                $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                $comments[]=$singular;
            }
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote an algorithm.'));
    }
    public function postVoteinlinecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('algorithm_id');
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('inline_comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('inline_comment_votes')
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update inline_algorithm_comments set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into inline_comment_votes (user_id, comment_id, algorithm_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $algorithm_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update inline_comment_votes set vote = ?, updated_at = ? where user_id = ? and algorithm_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $algorithm_id, $comment_id));
            }
            
            $downvotes = DB::table('inline_comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('inline_comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update inline_algorithm_comments set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function postVoteprofilecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $profile_id = Request::input('profile_id');
            if($profile_id == "me") {
                $profile_id = Auth::user()->id;
            }
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('profile_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('profile_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('profile_comment_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('profile_comment_votes')
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update profile_discussion set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('profile_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into profile_comment_votes (user_id, comment_id, profile_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $profile_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update profile_comment_votes set vote = ?, updated_at = ? where user_id = ? and profile_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $profile_id, $comment_id));
            }
            
            $downvotes = DB::table('profile_comment_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('profile_comment_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update profile_discussion set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function postVotecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('algorithm_id');
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('comment_votes')
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update algorithm_discussion set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into comment_votes (user_id, comment_id, algorithm_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $algorithm_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update comment_votes set vote = ?, updated_at = ? where user_id = ? and algorithm_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $algorithm_id, $comment_id));
            }
            
            $downvotes = DB::table('comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('comment_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update algorithm_discussion set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function postVoteprofilereply() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $profile_id = Request::input('profile_id');
            if($profile_id == "me") {
                $profile_id = Auth::user()->id;
            }
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('profile_reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('profile_reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('profile_reply_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('profile_reply_votes')
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update algorithm_discussion_replies set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('profile_reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('profile_id','=',$profile_id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into profile_reply_votes (user_id, comment_id, profile_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $profile_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update profile_reply_votes set vote = ?, updated_at = ? where user_id = ? and profile_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $profile_id, $comment_id));
            }
            
            $downvotes = DB::table('profile_reply_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('profile_reply_votes')
                ->where('profile_id','=',$profile_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update profile_discussion_replies set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function postVotereply() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('algorithm_id');
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('reply_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
                $upvotes = DB::table('reply_votes')
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update algorithm_discussion_replies set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('reply_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('algorithm_id','=',$algorithm_id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into reply_votes (user_id, comment_id, algorithm_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $algorithm_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update reply_votes set vote = ?, updated_at = ? where user_id = ? and algorithm_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $algorithm_id, $comment_id));
            }
            
            $downvotes = DB::table('reply_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('reply_votes')
                ->where('algorithm_id','=',$algorithm_id)
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update algorithm_discussion_replies set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function postRespondtoprofilecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $profile_id = Request::input('id');
            if($profile_id == "me") {
                $profile_id = Auth::user()->id;
            }
            $comment = Request::input('comment');
            $comment_id = Request::input('commentid');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into profile_discussion_replies (user_id, profile_id, comment_id, text, deleted, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $profile_id,
                $comment_id,
                $comment,
                FALSE,
                $time,
                $time)
            );
            
            $comment_id_second = DB::table('profile_discussion_replies')
                ->where('profile_id', $profile_id)
                ->where('comment_id', $comment_id)
                ->where('created_at', $time)
                ->where('user_id', Auth::user()->id)
                ->first();
            $comment_id_second = $comment_id_second->id;
            if($profile_id != Auth::user()->id) { 
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $profile_id, 
                    Auth::user()->id,
                    '/profile/'.$profile_id,
                    "New reply!",
                    "replied to something you said.",
                    $comment,
                    FALSE,
                    $comment_id."_".$comment_id_second,
                    $time,
                    $time)
                );     
            }
            
            $send_to_users = DB::table('profile_discussion_replies')
                ->where('comment_id', '=', $comment_id)
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                if($array->user_id != Auth::user()->id && $array->user_id != $profile_id) { 
                    DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                        $array->user_id, 
                        Auth::user()->id,
                        '/profile/'.$profile_id,
                        "New reply!",
                        "also replied to a comment you replied to.",
                        $comment,
                        FALSE,
                        $comment_id."_".$comment_id_second,
                        $time,
                        $time)
                    );
                    
                }
            }
            $comments_unfiltered = DB::table('profile_discussion')
                ->where('profile_id', '=', $profile_id)
                ->orderBy('created_at', 'desc')
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
                $aingular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','profile_discussion')
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
                $singular["upvotes"] = $array->upvotes;
                $singular["downvotes"] = $array->downvotes;
                $singular["created_at"] = $array->created_at;
                $singular["replies"] = array(); 
                $reply_comments_unfiltered = DB::table('profile_discussion_replies')
                    ->where('profile_id', '=', $profile_id)
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
                        ->where('tbl','=','profile_discussion_replies')
                        ->where('reported_id','=',$secondaryArray->id)
                        ->where('reported_user_id','=',$secondaryArray->user_id)
                        ->count();
                    $secondarySingular["upvotes"] = $secondaryArray->upvotes;
                    $secondarySingular["downvotes"] = $secondaryArray->downvotes;
                    $secondarySingular["created_at"] = $secondaryArray->created_at;
                    $secondaryName = DB::select('select * from users where id = ?', array($secondaryArray->user_id));
                    $secondarySingular["name"] = $secondaryName[0]->last_name." ".$secondaryName[0]->first_name;
                    $singular["replies"][] = $secondarySingular;
                }
                
                $name = DB::select('select * from users where id = ?', array($array->user_id));
                $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                $comments[]=$singular;
            }
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to able to respond to a comment.'));
    }
    public function postRespondtocomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_id = Request::input('id');
            $comment = Request::input('comment');
            $comment_id = Request::input('commentid');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into algorithm_discussion_replies (user_id, algorithm_id, comment_id, text, deleted, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', array(
                Auth::user()->id, 
                $algorithm_id,
                $comment_id,
                $comment,
                FALSE,
                $time,
                $time)
            );
            
            $notification_user_id = DB::table('algorithm_discussion')->where('id', $comment_id)->first();
            $notification_user_id = $notification_user_id->user_id;
            $comment_id_second = DB::table('algorithm_discussion_replies')
                ->where('algorithm_id', $algorithm_id)
                ->where('comment_id', $comment_id)
                ->where('created_at', $time)
                ->where('user_id', Auth::user()->id)
                ->first();
            $comment_id_second = $comment_id_second->id;
            if($notification_user_id != Auth::user()->id) { 
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $notification_user_id, 
                    Auth::user()->id,
                    '/posts/'.$algorithm_id,
                    "New reply!",
                    "replied to something you said.",
                    $comment,
                    FALSE,
                    $comment_id."_".$comment_id_second,
                    $time,
                    $time)
                );     
            }
            
            $send_to_users = DB::table('algorithm_discussion_replies')
                ->where('comment_id', '=', $comment_id)
                ->where('user_id', '!=', Auth::user()->id)
                ->get();
            foreach ($send_to_users as $array) {
                if($array->user_id != Auth::user()->id && $array->user_id != $notification_user_id) { 
                    DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                        $array->user_id, 
                        Auth::user()->id,
                        '/posts/'.$algorithm_id,
                        "New reply!",
                        "also replied to a comment you replied to.",
                        $comment,
                        FALSE,
                        $comment_id."_".$comment_id_second,
                        $time,
                        $time)
                    );
                    
                }
            }
            $comments_unfiltered = DB::table('algorithm_discussion')
                ->where('algorithm_id', '=', $algorithm_id)
                ->orderBy('created_at', 'desc')
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
                $singular["created_at"] = $array->created_at;
                $singular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','algorithm_discussion')
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
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
                    $secondarySingular["reported"] = DB::table('reports')
                        ->where('user_id','=',Auth::user()->id)
                        ->where('tbl','=','algorithm_discussion_replies')
                        ->where('reported_id','=',$secondaryArray->id)
                        ->where('reported_user_id','=',$secondaryArray->user_id)
                        ->count();
                    if($secondarySingular["user_id"] == Auth::user()->id) {
                        $secondarySingular["canDelete"] = true;
                    } else {
                        $secondarySingular["canDelete"] = false;
                    }
                    $secondarySingular["upvotes"] = $secondaryArray->upvotes;
                    $secondarySingular["downvotes"] = $secondaryArray->downvotes;
                    $secondarySingular["created_at"] = $secondaryArray->created_at;
                    $secondaryName = DB::select('select * from users where id = ?', array($secondaryArray->user_id));
                    $secondarySingular["name"] = $secondaryName[0]->last_name." ".$secondaryName[0]->first_name;
                    $singular["replies"][] = $secondarySingular;
                }
                
                $name = DB::select('select * from users where id = ?', array($array->user_id));
                $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                $comments[]=$singular;
            }
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to able to respond to a comment.'));
    }
    public function getTemplatedata() {
        $algorithmId = Request::input('id');
        $returnData = array();
        $found = DB::table('algorithms')
                    ->where('id', '=', $algorithmId)
                    ->where('template', '=', 1)
                    ->count();
        if($found==1) {
            $unparsedData = DB::select('select * from algorithms where id = ?', array($algorithmId));
            $returnData["name"] = $unparsedData[0]->name;
            $returnData["original_link"] = $unparsedData[0]->original_link;
            $returnData["content"] = $unparsedData[0]->content;
            $returnData["description"] = $unparsedData[0]->description;
            $returnData["language"] = $unparsedData[0]->language;
            $returnData["creator_id"] = $unparsedData[0]->user_id;
            $returnData["algorithm_id"] = $algorithmId;
            $returnData["request_id"] = $unparsedData[0]->request_id;
            return Response::json($returnData);
        }
        return Response::json(array('data'=>$found));
    }
    public function getViewrequests() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $unparsedData = DB::select('select * from algorithm_requests');
            $requests = array();
            $requests["data"]=array();
            foreach ($unparsedData as $array) {
                $singular = array();
                $singular["id"] = $array->id;
                $singular["user_id"] = $array->user_id;
                $name = DB::select('select * from users where id = ?', array($array->user_id));
                $singular["username"] = $name[0]->last_name." ".$name[0]->first_name;
                $singular["name"] = $array->name;
                $singular["language"] = $array->language;
                $singular["description"] = $array->description;
                $singular["upvotes"] = $array->upvotes;
                $found = DB::table('algorithm_request_votes')->where('request_id','=',$array->id)->where('user_id','=',Auth::user()->id)->count();
                $singular["userVote"]=$found;
                
                $singular["reported"] = DB::table('reports')
                    ->where('user_id','=',Auth::user()->id)
                    ->where('tbl','=','requests')   
                    ->where('reported_id','=',$array->id)
                    ->where('reported_user_id','=',$array->user_id)
                    ->count();
                $requests["data"][]=$singular;
            }
            return Response::json($requests);
        }
    }
    public function getUserdata() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $returnData = DB::table('users')
                ->where('id','=',Auth::user()->id)
                ->select('id','first_name','last_name','user_type')
                ->first();
            return Response::json(array('state' => 'success', 'message'=>'User data retrieved.','data'=>$returnData));    
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You are not logged in.'));
        }
    }
    public function getNotifications() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $notifications_unfiltered = DB::table('notifications')
                ->where('user_id', '=', Auth::user()->id)
                ->orderBy('id', 'desc')
                ->get();
            $notifications = array();
            foreach ($notifications_unfiltered as $array) {
                $singular = array();
                $singular["id"] = $array->id;
                $singular["user_id"] = $array->user_id;
                $singular["who_said"] = $array->who_said;
                $singular["url"] = $array->url;
                $singular["title"] = $array->title;
                $singular["text"] = $array->text;
                $singular["seen"] = $array->seen;
                $singular["what_was_said"] = $array->what_was_said;
                $singular["checked_out"] = $array->checked_out;
                $singular["reference"] = $array->reference;
                $singular["created_at"] = $array->created_at;
                $name = DB::select('select * from users where id = ?', array($array->who_said));
                $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                $notifications[]=$singular;
            }
            $returnData = array();
            $returnData["notifications"]=$notifications;
            $crumb_unparsed = DB::table('private_messages')
                ->where('to_id', '=', Auth::user()->id)
                ->where('seen', '=', 0)
                ->count();
            $returnData["messageCount"] = $crumb_unparsed;
            $crumb_unparsed = DB::table('group_members')
                ->where('member_id', '=', Auth::user()->id)
                ->where('read_last_message', '=', 0)
                ->where('accepted','=',1)
                ->count();
            $returnData["groupCount"] = $crumb_unparsed;
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive notifications.'));
        }
    }
    public function getGroupcrumb() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $returnData = array();
            $memberGroups = DB::table('groups')
                ->join('group_members', function($join)
                {
                    $join->on('groups.id', '=', 'group_members.group_id')
                         ->where('group_members.accepted', '=', 1)
                         ->where('group_members.member_id', '=', Auth::user()->id);
                })
                ->select(
                    'groups.id as group_id', 
                    'groups.group_name as group_name',
                    'group_members.read_last_message as read'
                )
                ->get();
            $returnData["crumb"] = $memberGroups;
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive this data.'));
        }
    }
    public function putSeeallnotifications() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $time = date('Y-m-d H:i:s');
            DB::update('update notifications set seen = 1, updated_at = ? where user_id = ?', array(
                $time, 
                Auth::user()->id, 
            ));
        }
    }
    public function putChecknotification() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update notifications set checked_out = 1, seen = 1, updated_at = ? where id = ? and user_id = ?', array(
                $time, 
                $id,
                Auth::user()->id
            ));
            return Response::json(array('state'=>'success', 'checked_out'=>$id));
        }
    }
    public function deleteDeletenotification() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            DB::delete('delete from notifications where id = ? and user_id = ?', array($id, Auth::user()->id));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postDeleteprofilecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update profile_discussion set deleted = 1, updated_at = ? where id = ?', array(
            $time, 
            $id 
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postDeleteprofilereply() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update profile_discussion_replies set deleted = 1, updated_at = ? where id = ?', array(
            $time, 
            $id
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postDeletecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update algorithm_discussion set deleted = 1, updated_at = ? where id = ?', array(
            $time, 
            $id 
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postDeletereply() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update algorithm_discussion_replies set deleted = 1, updated_at = ? where id = ?', array(
            $time, 
            $id
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postDeletelinecomment() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            return Response::json(array('state'=>'success', 'deleted'=>$id));
            DB::update('update inline_algorithm_comments set deleted = 1, updated_at = ? where id = ?', array(
            $time, 
            $id
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function putCommend() {
        $id = Input::get('id');
        if(Auth::check() && Auth::user()->user_type > 0) {
            $time = date('Y-m-d H:i:s');
            $commended = DB::table('user_commendations')
                ->where('user_id','=', $id)
                ->where('commendator','=', Auth::user()->id)
                ->count();
            if($commended == 1) {
                DB::delete('delete from user_commendations where user_id = ? and commendator = ?', array($id, Auth::user()->id));
            } else {
                DB::insert('insert into user_commendations (user_id, commendator, created_at, updated_at) values (?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    $time,
                    $time)
                );
            }
            $number = DB::table('user_commendations')
                ->where('user_id','=', $id)
                ->count();
            return Response::json(array('state' => 'success', 'number'=>$number));
        }
    }
    public function getMessagehistory() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $found = DB::table('users')
                ->where('id','=',$id);
            $returnData = array();
            if($found->count()==0) {
                $returnData["state"] = 'failure';
                $returnData["message"] = 'There is no user with the provided id.';
            } else {
                $returnData["state"] = 'success';
                $returnData["message"] = 'User found. Returning message history';
                $found = DB::table('users')
                    ->where('id','=',$id)
                    ->first();
                $returnData["talkingTo"] = $found->last_name." ".$found->first_name;
                $history_unparsed = DB::table('private_messages')
                    ->where('from_id', '=', Auth::user()->id)
                    ->where('to_id', '=', $id)
                    ->orWhere(function($query)
                    {
                        $query->where('from_id', '=', Request::input('id'))
                              ->where('to_id', '=', Auth::user()->id);
                    })
                    ->get();
                $history = array();
                foreach($history_unparsed as $array) {
                    $singular = array();
                    $singular["timestamp"] = $array->created_at;
                    if($array->from_id ==Auth::user()->id) {
                        $singular["from_me"] = TRUE;
                    } else {
                        $singular["from"] = FALSE;
                    }
                    $name = DB::select('select * from users where id = ?', array($array->from_id));
                    $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                    $singular["id"] = $array->from_id;
                    $singular["message"] = $array->message;
                    $singular["seen"] = $array->seen;
                    $history[] = $singular;
                }
                $returnData["history"]=$history;
            }
            $returnData["timestamp"] = DB::table('private_messages')
                ->where('from_id', '=', Auth::user()->id)
                ->orWhere(function($query)
                {
                    $query->where('to_id', '=', Auth::user()->id);
                })
                 ->orderBy('updated_at', 'desc')
                ->first()
                ->updated_at;
            $crumb_unparsed = DB::table('private_messages')
                ->where('from_id', '=', Auth::user()->id)
                ->orWhere(function($query)
                {
                    $query->where('to_id', '=', Auth::user()->id);
                })
                 ->orderBy('created_at', 'desc')
                ->get();
            
            $crumbs = array();
            $added = array();
            foreach($crumb_unparsed as $array) {
                $singular = array();
                if($array->to_id == Auth::user()->id) {
                    if(!in_array($array->from_id,$added)) {
                        $added[] = $array->from_id;
                        $singular["link"] = $array->from_id;
                        $name = DB::select('select * from users where id = ?', array($array->from_id));
                        $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                        $singular["from"] = $singular["name"];
                        $singular["message"] = $array->message;
                        $singular["timestamp"] = $array->updated_at;
                        $singular["seen"] = $array->seen;
                        $crumbs[] = $singular;
                    }
                } else if($array->from_id == Auth::user()->id) {
                    if(!in_array($array->to_id,$added)) {
                        $added[] = $array->to_id;
                        $singular["link"] = $array->to_id;
                        $name = DB::select('select * from users where id = ?', array($array->to_id));
                        $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                        $singular["from"] = "You";
                        $singular["message"] = $array->message;
                        $singular["timestamp"] = $array->updated_at;
                        $singular["seen"] = 1;
                        $crumbs[] = $singular;
                    }
                }
            }
            $returnData["crumb"]=$crumbs;
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive messages.'));
        }
    }
    public function postKickfromgroup() {
        //{id:groupID, userid: userid},
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $userId = Request::input('userid');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->count();
            $groupName = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->first()
                ->group_name;
            if($count==1) {
                DB::delete('delete from group_members where group_id = ? and member_id = ?', array($groupId, $userId));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $userId, 
                    Auth::user()->id,
                    '/groups/'.$groupId,
                    "Kicked from group!",
                    "kicked you from <span><strong>".$groupName."</strong></span>.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array('state'=>'success','message'=>'User kicked.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }
        } else {
            return Response::json(array('state'=>'failure','message'=>'You must be logged in to kick people from groups.'));
        }
    }
    public function postAcceptgrouprequest() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $userId = Request::input('userid');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->count();
            $groupName = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->first()
                ->group_name;
            if($count==1) {
                DB::update('update group_members set accepted = 1 where group_id = ? and member_id = ?', array($groupId, $userId));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $userId, 
                    Auth::user()->id,
                    '/groups/'.$groupId,
                    "Join request accepted!",
                    "accepted you in <span><strong>".$groupName."</strong></span>.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array('state'=>'success','message'=>'User accepted in group.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }    
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to accept join requests.'));
    }
    public function postConvertgroupprivate() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->where('private','=',0)
                ->count();
            if($count==1) {
                DB::update('update groups set private = 1, updated_at = ? where id = ?', array($time, $groupId));
                return Response::json(array('state'=>'success','message'=>'Group is now private.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }    
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in convert.'));
    }
    public function postConvertgrouppublic() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->where('private','=',1)
                ->count();
            if($count==1) {
                DB::update('update groups set private = 0, updated_at = ? where id = ?', array($time, $groupId));
                $update_users = DB::table('group_members')
                    ->where('group_id','=',$groupId)
                    ->where('accepted','=',0)
                    ->get();
                $groupName = DB::table('groups')
                    ->where('leader','=',Auth::user()->id)
                    ->where('id','=',$groupId)
                    ->first()
                    ->group_name;
                foreach($update_users as $array) {
                    $userId = $array->member_id;
                    DB::update('update group_members set accepted = 1, updated_at = ? where group_id = ? and accepted = 0 and member_id = ?', array($time, $groupId, $userId));
                    DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                        $userId, 
                        Auth::user()->id,
                        '/groups/'.$groupId,
                        "Join request accepted!",
                        "accepted you in <span><strong>".$groupName."</strong></span>.",
                        "",
                        FALSE,
                        "",
                        $time,
                        $time)
                    );  
                }
                return Response::json(array('state'=>'success','message'=>'Group is now private.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }    
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in convert.'));
    }
    public function postPromotetoleader() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $userId = Request::input('userid');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->count();
            $groupName = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->first()
                ->group_name;
            if($count==1) {
                DB::update('update groups set leader = ? where id = ?', array($userId, $groupId));
                DB::update('update group_members set is_leader = FALSE where group_id = ? and member_id = ? ', array($groupId, Auth::user()->id));
                DB::update('update group_members set is_leader = TRUE where group_id = ? and member_id = ? ', array($groupId, $userId));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $userId, 
                    Auth::user()->id,
                    '/groups/'.$groupId,
                    "Promoted to leader!",
                    "promoted you to leader in <span><strong>".$groupName."</strong></span>.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array('state'=>'success','message'=>'User denied entrance in group.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }    
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to accept join requests.'));
    }
    public function postDenygrouprequest() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $groupId = Request::input('id');
            $userId = Request::input('userid');
            $time = date('Y-m-d H:i:s');
            $count = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->count();
            $groupName = DB::table('groups')
                ->where('leader','=',Auth::user()->id)
                ->where('id','=',$groupId)
                ->first()
                ->group_name;
            if($count==1) {
                DB::delete('delete from group_members where group_id = ? and member_id = ? and accepted = 0', array($groupId, $userId));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $userId, 
                    Auth::user()->id,
                    '/groups/'.$groupId,
                    "Join request denied!",
                    "Denied your join request to <span><strong>".$groupName."</strong></span>.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array('state'=>'success','message'=>'User denied entrance in group.'));
            } else {
                return Response::json(array('state'=>'failure','message'=>'Something unexpected happened.'));
            }    
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to accept join requests.'));
    }
    public function getGroupinitialdata() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $found = DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('member_id','=',Auth::user()->id)
                ->where('accepted','=',1)
                ->count();
            $returnData = array();
            
            if($found ==0) {
                $found = DB::table('groups')
                ->where('id','=',$id)
                ->count();
                if($found == 0) {
                    $returnData["state"] = 'failure';
                    $returnData["message"] = 'No group with the provided id exists.';
                } else {
                    $returnData["state"] = 'success';
                    $returnData["message"] = 'Group found. You are not in the group. Returning available data.';
                    $found = DB::table('groups')
                        ->where('id','=',$id)
                        ->first();
                    $returnData["requested"] = DB::table('group_members')
                        ->where('accepted','=',0)
                        ->where('member_id','=',Auth::user()->id)
                        ->where('group_id','=',$id)
                        ->count();
                       // 
                        //
                    $returnData["groupName"] = $found->group_name;
                    $returnData["description"] = $found->description;
                    $returnData["privateGroup"] = $found->private;
                    $returnData["leader_id"] = $found->leader;
                    $found = DB::table('users')
                        ->where('id','=',$found->leader)
                        ->first();
                    $returnData["leader_name"] = $found->last_name." ".$found->first_name;
                    $returnData["members"] = DB::table('group_members')
                        ->where('group_id','=',$id)
                        ->where('accepted','=',1)
                        ->join('users', function($join)
                            {
                                $join->on('group_members.member_id', '=', 'users.id');
                            })
                        ->select(
                            'users.first_name as first_name',
                            'users.last_name as last_name',
                            'users.id as id',
                            'group_members.updated_at as since'
                        )
                        ->get();

                }
            } else {
                $returnData["state"] = 'success';
                $returnData["message"] = 'Group found. Returning message history';
                $found = DB::table('groups')
                    ->where('id','=',$id)
                    ->first();
                $returnData["groupName"] = $found->group_name;
                $returnData["description"] = $found->description;
                $returnData["privateGroup"] = $found->private;
                if($found->leader == Auth::user()->id) {
                    $returnData["leader_me"] = TRUE;
                } else {
                    $returnData["leader_me"] = FALSE;
                }
                $returnData["me"] = Auth::user()->id;
                $returnData["leader_id"] = $found->leader;
                $found = DB::table('users')
                    ->where('id','=',$found->leader)
                    ->first();
                $returnData["leader_name"] = $found->last_name." ".$found->first_name;
                $returnData["members"] = DB::table('group_members')
                    ->where('group_id','=',$id)
                    ->where('accepted','=',1)
                    ->join('users', function($join)
                        {
                            $join->on('group_members.member_id', '=', 'users.id');
                        })
                    ->select(
                        'users.first_name as first_name',
                        'users.last_name as last_name',
                        'users.id as id',
                        'group_members.updated_at as since'
                    )
                    ->get();
                if($returnData["leader_me"]==TRUE) {
                    $returnData["active_requests"] = DB::table('group_members')
                        ->where('group_id','=',$id)
                        ->where('accepted','=',0)
                        ->join('users', function($join)
                            {
                                $join->on('group_members.member_id', '=', 'users.id');
                            })
                        ->select(
                            'users.first_name as first_name',
                            'users.last_name as last_name',
                            'users.id as id',
                            'group_members.updated_at as since'
                        )
                        ->get();
                }
                $history_unparsed = DB::table('group_messages')
                    ->where('group_id', '=', $id)
                    ->get();
                $history = array();
                foreach($history_unparsed as $array) {
                    $singular = array();
                    $singular["timestamp"] = $array->created_at;
                    $name = DB::select('select * from users where id = ?', array($array->user_id));
                    $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                    $singular["id"] = $array->user_id;
                    $singular["message"] = $array->message;
                    $history[] = $singular;
                }
                $returnData["history"]=$history;
            }
            
            $returnData["timestamp"] = DB::table('group_messages')
                ->where('group_id', '=', $id)
                ->count();
            if($returnData["timestamp"] > 0) {
                $returnData["timestamp"] = DB::table('group_messages')
                ->where('group_id', '=', $id)
                ->orderBy('updated_at', 'desc')
                ->first()
                ->updated_at;
            }
            $memberGroups = DB::table('groups')
                ->join('group_members', function($join)
                {
                    $join->on('groups.id', '=', 'group_members.group_id')
                         ->where('group_members.accepted', '=', 1)
                         ->where('group_members.member_id', '=', Auth::user()->id);
                })
                ->select(
                    'groups.id as group_id', 
                    'groups.group_name as group_name',
                    'group_members.read_last_message as read'
                )
                ->get();
            $returnData["crumb"] = $memberGroups;
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive messages.'));
        }
    }
    public function postMessagegroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $comment = Request::input('comment');
            $time = date('Y-m-d H:i:s');
            
            $memberCheck = DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('member_id','=',Auth::user()->id)
                ->where('accepted','=',1)
                ->count();
            if($memberCheck==1) {
                DB::insert('insert into group_messages (group_id, user_id, message, created_at, updated_at) values (?, ?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    $comment,
                    $time,
                    $time
                ));
                DB::update('update group_members set read_last_message = 0, updated_at = ? where group_id = ? and accepted = 1 and member_id <> ?', array(
                    $time, 
                    $id,
                    Auth::user()->id
                ));
                return Response::json(array('state' => 'success', 'message'=>'Message sent.', 'id'=>$id, 'comment'=>$comment, 'time'=>$time));
            } 
            return Response::json(array('state' => 'failure', 'message'=>'You must be a member of the group in order to send messages.'));
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to send messages.'));
        }
    }
    public function postMessageuser() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $id = Request::input('id');
            $comment = Request::input('comment');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into private_messages (from_id, to_id, message, created_at, updated_at) values (?, ?, ?, ?, ?)', array(
                Auth::user()->id,
                $id,
                $comment,
                $time,
                $time
            ));
            DB::update('update private_messages set seen = 1, updated_at = ? where to_id = ? and from_id = ?', array(
                $time, 
                Auth::user()->id,
                $id
            ));
            return Response::json(array('state' => 'success', 'message'=>'Message sent.', 'id'=>$id, 'comment'=>$comment, 'time'=>$time));
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to send messages.'));
        }
    }
    public function postSearchgroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $value = Request::input('search');
            if(strlen($value)>0) {
                $unparsedReturnData = DB::table('groups')
                    ->where('group_name','like','%'.$value.'%')
                    ->orWhere('description','like','%'.$value.'%')
                    ->get();
            } else {
                $unparsedReturnData = DB::table('groups')
                    ->get();
            }
            $returnData = array();
            foreach($unparsedReturnData as $array) {
                $name = DB::table('users')
                    ->where('id','=',$array->leader)
                    ->first();
                if($array->leader == Auth::user()->id) {
                    $array->leader_me = 1;
                } else {
                    $array->leader_me = 0;
                }
                $array->leader_name = $name->last_name." ".$name->first_name;
                $memberCount = DB::table('group_members')
                    ->where('group_id','=',$array->id)
                    ->count();
                $array->memberCount = $memberCount;
                $array->ownData = DB::table('group_members')
                    ->where('group_id','=',$array->id)
                    ->where('member_id','=',Auth::user()->id)
                    ->first();
                if($array->visible == 1) {
                    $returnData[]=$array;
                }
            }
            return Response::json($returnData);
        }
    }
    public function postLeavegroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $time = date('Y-m-d H:i:s');
            DB::delete('delete from group_members where group_id = ? and member_id = ?', array($id, Auth::user()->id));
            $count = DB::table('group_members')
                ->where('group_id','=',$id)
                ->count();
            if($count != 0) {
                $leader_me_check = DB::table('groups')
                    ->where('id','=',$id)
                    ->where('leader','=',Auth::user()->id)
                    ->count();
                if($leader_me_check == 1) {
                    $new_leader = DB::table('group_members')
                        ->where('group_id','=',$id)
                        ->first()->member_id;
                    DB::update('update groups set leader = ?, updated_at = ? where id = ?', array(
                        $new_leader,
                        $time, 
                        $id
                    ));
                    return Response::json(array("state"=>"success","message"=>"left group"));
                }
            } else {
                DB::update('update groups set visible = ?, updated_at = ? where id = ?', array(
                    FALSE,
                    $time,
                    $id
                ));
                return Response::json(array("state"=>"success","message"=>"group deleted"));
            }
            return Response::json(array($id,$time));
        }
    }
    public function getGetmygroups() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $unparsed_groups = DB::table('groups')
                ->join('group_members', function($join)
                {
                    $join->on('groups.id', '=', 'group_members.group_id')
                         ->where('group_members.member_id', '=', Auth::user()->id);
                })
                ->select(
                    'groups.id as group_id', 
                    'groups.group_name as group_name',
                    'groups.description as group_description',
                    'groups.leader as leader_id', 
                    'group_members.accepted as accepted',
                    'group_members.updated_at as since',
                    'groups.private as private',
                    'groups.visible as visible',
                    'group_members.is_leader as leader_me')
                ->get();
            $returnData = array();
            foreach($unparsed_groups as $array) {
                $singular = array();
                $memberCount = DB::table('group_members')
                    ->where('group_id','=',$array->group_id)
                    ->count();
                $name = DB::table('users')
                    ->where('id','=',$array->leader_id)
                    ->first();
                $array->leader_name = $name->last_name." ".$name->first_name;
                $array->memberCount = $memberCount;
             }
            $returnData = $unparsed_groups;
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive data about your groups.'));
        }
    }
    public function postCancelrequest() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            DB::delete('delete from group_members where group_id = ? and member_id = ?', array($id, Auth::user()->id));
            return Response::json(array('state' => 'success', 'message'=>'canceled request'));
        }
    }
    public function postJoingroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $time = date('Y-m-d H:i:s');
            $getType = DB::table('groups')
                ->where('id','=',$id)
                ->first()
                ->private;
            if($getType==0) {
                DB::insert('insert into group_members (group_id, member_id, accepted, is_leader, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    TRUE,
                    FALSE,
                    $time,
                    $time
                ));
            } 
            if($getType ==1) {
                DB::insert('insert into group_members (group_id, member_id, accepted, is_leader, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    FALSE,
                    FALSE,
                    $time,
                    $time
                ));
            }
            $ownData = DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('member_id','=',Auth::user()->id)
                ->first();
            return Response::json($ownData);    
        }
    }
    public function postCreategroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $name = Request::input('name');
            $description = Request::input('description');
            $type = Request::input('type');
            $time = date('Y-m-d H:i:s');
            DB::insert('insert into groups (group_name, description, private, leader, visible, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)', array(
                $name,
                $description,
                $type,
                Auth::user()->id,
                TRUE,
                $time,
                $time
            ));
            $returnId = DB::table('groups') 
                ->where('group_name','=',$name)
                ->where('private','=',$type)
                ->where('created_at','=',$time)
                ->where('updated_at','=',$time)
                ->where('description','=',$description)
                ->where('leader','=',Auth::user()->id)
                ->first()->id;
            DB::insert('insert into group_members (group_id, member_id, accepted, is_leader, created_at, updated_at) values (?, ?, ?, ?, ?, ?)', array(
                $returnId,
                Auth::user()->id,
                TRUE,
                TRUE,
                $time,
                $time
            ));
            return Response::json(array('state' => 'success', 'message'=>'Group created.', 'id'=>$returnId));
        }
    }
    public function getAdmindata() {
        if(Auth::check() && Auth::user()->user_type >1) {
            $returnData = array();
            $returnData["userlist"] = DB::table('users')
                ->get();
            $reportsUnfiltered = DB::table('reports')
                ->get();
            $returnData["reports"] = array();
            foreach($reportsUnfiltered as $array) {
                if($array->answered == 0) {
                    $singular = array();
                    $singular["id"] = $array->id;
                    $singular["created_at"] = $array->created_at;
                    $singular["answered"] = $array->answered;
                    $singular["reporter_id"] = $array->user_id;
                    $reporter = DB::table('users')
                        ->where('id','=',$array->user_id)
                        ->first();
                    $singular["reporter_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reported_id"] = $array->reported_user_id;
                    $reporter = DB::table('users')
                        ->where('id','=',$array->reported_user_id)
                        ->first();
                    $singular["reported_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reason"] = $array->user_reason;
                    $singular["description"] = $array->user_description;
                    switch($array->tbl) {
                        case "algorithms":
                            $singular["reportedType"] = "Algorithm";
                            $singular["linkTo"] = "/posts/".$array->reported_id;
                            $singular["linkName"] = DB::table('algorithms')
                                ->where('id','=',$array->reported_id)
                                ->first()
                                ->name;
                            break;
                        case "requests":
                            $singular["reportedType"] = "Request";
                            $fetchData = DB::table('algorithm_requests')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["requestName"] = $fetchData->name;
                            $singular["requestLanguage"] = $fetchData->language;
                            $singular["requestDescription"] = $fetchData->description;
                            break;
                        case "inline_algorithm_comments":
                            $singular["reportedType"] = "Line Comment";
                            $fetchData = DB::table('inline_algorithm_comments')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["linkTo"] = "/posts/".$fetchData->algorithm_id;
                            $singular["linkName"] = DB::table('algorithms')
                                ->where("id","=",$fetchData->algorithm_id)
                                ->first()
                                ->name;
                            $singular["line"] = $fetchData->line;
                            $singular["text"] = $fetchData->text;
                            break;
                        case "algorithm_discussion":
                            $singular["reportedType"] = "Algorithm Comment";
                            $fetchData = DB::table('algorithm_discussion')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["linkTo"] = "/posts/".$fetchData->algorithm_id."#comment".$fetchData->id;
                            $singular["linkName"] = DB::table('algorithms')
                                ->where("id","=",$fetchData->algorithm_id)
                                ->first()
                                ->name;
                            $singular["text"] = $fetchData->text;
                            break;
                        case "algorithm_discussion_replies":
                            $singular["reportedType"] = "Algorithm Reply";
                            $fetchData = DB::table('algorithm_discussion_replies')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["linkTo"] = "/posts/".$fetchData->algorithm_id."#comment".$fetchData->comment_id."_".$fetchData->id;
                            $singular["linkName"] = DB::table('algorithms')
                                ->where("id","=",$fetchData->algorithm_id)
                                ->first()
                                ->name;
                            $singular["text"] = $fetchData->text;
                            break;
                        case "profile_discussion":
                            $singular["reportedType"] = "Profile Comment";
                            $fetchData = DB::table('profile_discussion')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["linkTo"] = "/profile/".$fetchData->profile_id."#comment".$fetchData->id;
                            $singular["linkName"] = $singular["reported_name"];
                            $singular["text"] = $fetchData->text;
                            break;
                        case "users":
                            $singular["reportedType"] = "Profile";
                            $singular["linkTo"] = "/profile/".$singular["reported_id"];
                            $singular["linkName"] = $singular["reported_name"];
                            break;
                        case "profile_discussion_replies":
                            $singular["reportedType"] = "Profile Reply";
                            $fetchData = DB::table('profile_discussion_replies')
                                ->where('id','=',$array->reported_id)
                                ->first();
                            $singular["linkTo"] = "/profile/".$fetchData->profile_id."#comment".$fetchData->comment_id."_".$fetchData->id;
                            $singular["linkName"] = $singular["reported_name"];
                            $singular["text"] = $fetchData->text;
                            break;

                    }
                    $returnData["reports"][] = $singular;
                }
            }
            return Response::json(array("state"=>"success", "message"=>"Data successfully loaded", "data"=> $returnData));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function putSetasanswered() {
        if(Auth::check() && Auth::user()->user_type >1) {
            $id = Request::input('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update reports set answered = 1, updated_at = ?, answered_by = ? where id = ?', array(
                $time, 
                Auth::user()->id,
                $id 
            ));
            return Response::json(array("state"=>"success", "message"=>"Report set as answered."));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function putUnbanuser() {
        if(Auth::check() && Auth::user()->user_type >1) {
            $id = Request::input('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update users set user_type = 1, updated_at = ? where id = ?', array(
                $time, 
                $id 
            ));
            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                $id, 
                Auth::user()->id,
                '/',
                "Unbanned!",
                "unbanned you.",
                "",
                FALSE,
                "",
                $time,
                $time)
            );

            return Response::json(array("state"=>"success", "message"=>"User successfully unbanned."));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function putPromoteuser() {
        if(Auth::check() && Auth::user()->user_type > 1) {
            $id = Request::input('id');
            $userType = DB::table('users')
                ->where('id','=',$id)
                ->select('user_type')
                ->first();
            $time = date('Y-m-d H:i:s');
            if($userType->user_type == 1) {
                DB::update('update users set user_type = 2, updated_at = ? where id = ?', array(
                    $time, 
                    $id 
                ));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    '/users/admin/',
                    "Moderator promotion!",
                    "promoted you to moderator status.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array("state"=>"success", "message"=>"User successfully promoted to moderator status."));
            }
            return Response::json(array("state"=>"failure", "message"=>"Insuficient priviledges."));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function putDemoteuser() {
        if(Auth::check() && Auth::user()->user_type === 3) {
            $id = Request::input('id');
            $userType = DB::table('users')
                ->where('id','=',$id)
                ->select('user_type')
                ->first();
            $time = date('Y-m-d H:i:s');
            if($userType->user_type == 2) {
                DB::update('update users set user_type = 1, updated_at = ? where id = ?', array(
                    $time, 
                    $id 
                ));
                DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $id,
                    Auth::user()->id,
                    '/',
                    "Demotion!",
                    "demoted you to normal user status.",
                    "",
                    FALSE,
                    "",
                    $time,
                    $time)
                );
                return Response::json(array("state"=>"success", "message"=>"User successfully promoted to moderator status."));
            }
            return Response::json(array("state"=>"failure", "message"=>"Insuficient priviledges."));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function putBanuser() {
        if(Auth::check() && Auth::user()->user_type >1) {
            $id = Request::input('id');
            $userType = DB::table('users')
                ->where('id','=',$id)
                ->select('user_type')
                ->first();
            $time = date('Y-m-d H:i:s');
            if($userType->user_type < Auth::user()->user_type) {
                DB::update('update users set user_type = 0, updated_at = ? where id = ?', array(
                    $time, 
                    $id 
                ));
                return Response::json(array("state"=>"success", "message"=>"User successfully banned."));
            }
            return Response::json(array("state"=>"failure", "message"=>"Insuficient priviledges."));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function postReport() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $user_id = Request::input('user_id');
            $reported_id = Request::input('reported_id');
            $tbl = Request::input('table');
            $reported_user_id = Request::input('reported_user_id');
            $user_reason = Request::input('user_reason');
            $user_description = Request::input('user_description');
            $time = date('Y-m-d H:i:s');
            
            $count = DB::table('reports')
                ->where('tbl','=',$tbl)
                ->where('user_id','=',$user_id)
                ->where('reported_user_id','=',$reported_user_id)
                ->where('reported_id','=',$reported_id)
                ->count();
            
            if($count == 0) {
                
                DB::insert('insert into reports (user_id, reported_id, tbl, reported_user_id, user_reason, user_description, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?)', array(
                    $user_id,
                    $reported_id,
                    $tbl,
                    $reported_user_id,
                    $user_reason,
                    $user_description,
                    $time,
                    $time
                ));
                
            }
            
            return Response::json(array('state' => 'success', 'message'=>$count));
            
        }
        return Response::json(array('state' => 'failure', 'message'=>'Insuficient priviledges.'));
    }
}

?>