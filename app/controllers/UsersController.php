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
        if(Auth::check()) {
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
        if(Auth::check()) {
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
        if(Auth::check()) {
            
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
        if(Auth::check()) {
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
        if(Auth::check()) {
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
            $comments_unfiltered = DB::table('algorithm_discussion')->where('algorithm_id', '=', $algorithm_id)->get();
            $comments = array();
            foreach ($comments_unfiltered as $array) {
                $singular = array();
                $singular["id"] = $array->id;
                $singular["user_id"] = $array->user_id;
                $singular["text"] = $array->text;
                $singular["deleted"] = $array->deleted;
                $singular["upvotes"] = $array->upvotes;
                $singular["downvotes"] = $array->downvotes;
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
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote an algorithm.'));
    }
    public function postCommentline() {
        if(Auth::check()) {
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
            $comments_unfiltered = DB::table('inline_algorithm_comments')->where('algorithm_id', '=', $algorithm_id)->get();
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
                $name = DB::select('select * from users where id = ?', array($array->user_id));
                $singular["name"] = $name[0]->last_name." ".$name[0]->first_name;
                $comments[]=$singular;
            }
            return Response::json($comments);
        } 
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote an algorithm.'));
    }
    public function postVoteinlinecomment() {
        if(Auth::check()) {
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
    public function postVotecomment() {
        if(Auth::check()) {
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
    public function postVotereply() {
        if(Auth::check()) {
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
    public function postRespondtocomment() {
        if(Auth::check()) {
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
            $comments_unfiltered = DB::table('algorithm_discussion')->where('algorithm_id', '=', $algorithm_id)->get();
            $comments = array();
            foreach ($comments_unfiltered as $array) {
                $singular = array();
                $singular["id"] = $array->id;
                $singular["user_id"] = $array->user_id;
                $singular["text"] = $array->text;
                $singular["deleted"] = $array->deleted;
                $singular["upvotes"] = $array->upvotes;
                $singular["downvotes"] = $array->downvotes;
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
        if(Auth::check()) {
            $unparsedData = DB::select('select * from algorithm_requests');
            $requests = array();
            $requests["data"]=array();
            foreach ($unparsedData as $array) {
                $singular = array();
                $singular["id"] = $array->id;
                $singular["name"] = $array->name;
                $singular["language"] = $array->language;
                $singular["description"] = $array->description;
                $singular["upvotes"] = $array->upvotes;
                $found = DB::table('algorithm_request_votes')->where('request_id','=',$array->id)->where('user_id','=',Auth::user()->id)->count();
                $singular["userVote"]=$found;
                $requests["data"][]=$singular;
            }
            return Response::json($requests);
        }
    }
    public function getNotifications() {
        if(Auth::check()) {
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
            return Response::json($notifications);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive notifications.'));
        }
    }
    public function putSeeallnotifications() {
        if(Auth::check()) {
            $time = date('Y-m-d H:i:s');
            DB::update('update notifications set seen = 1, updated_at = ? where user_id = ?', array(
                $time, 
                Auth::user()->id, 
            ));
        }
    }
    public function putChecknotification() {
        if(Auth::check()) {
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
        if(Auth::check()) {
            $id = Input::get('id');
            DB::delete('delete from notifications where id = ? and user_id = ?', array($id, Auth::user()->id));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function putCommend() {
        $id = Input::get('id');
        if(Auth::check()) {
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
}

?>