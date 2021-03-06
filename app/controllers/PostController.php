<?php

class PostController extends BaseController {
    
    public function postSearch() {
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
                    ->where('upvotes','>','downvotes')
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
    }
    public function getData() {
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
    }
    public function getMyalgorithms() {
        $algorithms_unfiltered = DB::table('algorithms')
            ->where('user_id', '=', Auth::user()->id)
            ->get();
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
    public function deleteDelete() {
        $algorithmId = Request::input('id');
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
    public function putPublish() {
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
    public function getEdit($algorithmId) {
        if(Auth::check()) {
            if(Auth::user()->user_type == 0) {
            Session::flush();
            return Redirect::to('/')
                ->withErrors(["This account is currently banned."]);
            }
            $found = DB::table('algorithms')
                ->where('user_id', '=', Auth::user()->id)
                ->where('id', '=', $algorithmId)
                ->where('template', '=', 1)
                ->count();
            if($found==1) {
                return View::make('edit');
            } else {
                return View::make('404')->withErrors(["This page cannot be reached because this post doesn't exist or it isn't owned by you."]);
            }
        }
    }
    public function getTemplate() {
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
    public function postEdit() {
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
            return Redirect::to('/post/edit/'.Input::get('algorithm_id'))->withErrors("Algorithm successfully updated.");
        }
    }
    public function postVote() {
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
    public function postVotecommentline() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $comment_id = Request::input('comment_id');
            $vote = Request::input('vote');
            $time = date('Y-m-d H:i:s');
            $found = DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->count();
            if($found == 1) {
                DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',$vote)
                    ->delete();
                $downvotes = DB::table('inline_comment_votes')
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',0)
                    ->count();
            
                $upvotes = DB::table('inline_comment_votes')
                    ->where('comment_id','=',$comment_id)
                    ->where('vote','=',1)
                    ->count(); 
                DB::update('update inline_algorithm_comments set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
            }
            $found = DB::table('inline_comment_votes')
                    ->where('user_id','=', Auth::user()->id)
                    ->where('comment_id','=',$comment_id)
                    ->count();
            if($found==0) {
                DB::insert('insert into inline_comment_votes (user_id, comment_id, vote, created_at, updated_at) values (?, ?, ?, ?, ?)', array(
                    Auth::user()->id, 
                    $comment_id,
                    $vote,
                    $time,
                    $time)
                );
            } else {
                DB::update('update inline_comment_votes set vote = ?, updated_at = ? where user_id = ? and comment_id = ?', array($vote, $time,  Auth::user()->id, $comment_id));
            }
            
            $downvotes = DB::table('inline_comment_votes')
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',0)
                ->count();
            
            $upvotes = DB::table('inline_comment_votes')
                ->where('comment_id','=',$comment_id)
                ->where('vote','=',1)
                ->count(); 
            DB::update('update inline_algorithm_comments set upvotes = ?, downvotes = ?, updated_at = ? where id = ?', array($upvotes, $downvotes, $time, $comment_id));
            return Response::json(array('state' => 'success', 'upvotes'=>$upvotes, 'downvotes'=>$downvotes));
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in and not banned to be able to vote a comment.'));
    }
    public function deleteDeletecommentline() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Input::get('id');
            $time = date('Y-m-d H:i:s');
            DB::update('update inline_algorithm_comments set deleted = 1, updated_at = ? where id = ?', array(
                $time, 
                $id
            ));
            return Response::json(array('state'=>'success', 'deleted'=>$id));
        }
    }
    public function postComment() {
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
    public function postReply() {
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
    
}

?>