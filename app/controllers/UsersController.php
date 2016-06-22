<?php

class UsersController extends BaseController {
    
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
            return Redirect::to('/')->withErrors($validator)->withInput();
        }
    }
    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
            if(Auth::check() && Auth::user()->user_type == 0) {
                Session::flush();
                return Redirect::to('/')
                    ->withErrors(["This account is currently banned."]);
            } else {
                return Redirect::to('/');
            }
        } else {
            //Failure
            return Redirect::to('/')
                ->withErrors(["Your email/password combination is invalid."])
                ->withInput();
        }     
    }
    public function getLogout() {
        Session::flush();
        return Redirect::to('/');
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
    public function getProfiledetails() {
        
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
            
            $comments_unfiltered = DB::table('profile_discussion')
                ->where('profile_id', '=', $id)
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
    public function postComment() {
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
    public function postVotecomment() {
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
    public function postVotereply() {
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
    public function postDeletecomment() {
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
    public function postDeletereply() {
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
    public function postReply() {
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
}

?>