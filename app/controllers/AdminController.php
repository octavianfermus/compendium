<?php

class AdminController extends Controller{
    
    public function getFetch() {
        
        if(Auth::check() && Auth::user()->user_type >1) {
            
            $returnData = array();
            $userlistUnfiltered = DB::table('users')
                ->get();
            foreach($userlistUnfiltered as $array) {
                
                $array->warnCount = DB::table('warnings')
                    ->where('user_id','=',$array->id)
                    ->count();
                $returnData["userlist"][] = $array;
            }
            $reportsUnfiltered = DB::table('reports')
                ->get();
            $returnData["reports"] = array();
            $returnData["answered"] = array();
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
                    $singular["reporter_type"]=$reporter->user_type;
                    $singular["reporter_warns"]=DB::table('warnings')
                        ->where('user_id','=',$array->user_id)
                        ->count();
                    $singular["reporter_warned"]=DB::table('warnings')
                        ->where('user_id','=',$array->user_id)
                        ->where('report_id','=',$array->id)
                        ->count();
                    $singular["reporter_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reported_id"] = $array->reported_user_id;
                    $singular["reported_warns"]=DB::table('warnings')
                        ->where('user_id','=',$array->reported_user_id)
                        ->count();
                    $reporter = DB::table('users')
                        ->where('id','=',$array->reported_user_id)
                        ->first();
                    
                    $singular["reported_warned"]=DB::table('warnings')
                        ->where('user_id','=',$array->reported_user_id)
                        ->where('report_id','=',$array->id)
                        ->count();
                    $singular["reported_type"]=$reporter->user_type;
                    $singular["reported_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reason"] = $array->user_reason;
                    $singular["description"] = $array->user_description;
                    switch($array->tbl) {
                        case "algorithms":
                            $singular["reportedType"] = "Algorithm";
                            $singular["linkName"] = DB::table('algorithms')
                                ->where('id','=',$array->reported_id)
                                ->get();
                            if(count($singular["linkName"])==0) {
                                $singular["linkName"] = "Algorithm was deleted!";
                                $singular["linkTo"] = "";
                                break;
                            }
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
                } else {
                    $singular = array();
                    $singular["id"] = $array->id;
                    $singular["created_at"] = $array->created_at;
                    $singular["answered"] = $array->answered;
                    $singular["reporter_id"] = $array->user_id;
                    $reporter = DB::table('users')
                        ->where('id','=',$array->user_id)
                        ->first();
                    $singular["reporter_type"]=$reporter->user_type;
                    $singular["reporter_warned"]=DB::table('warnings')
                        ->where('user_id','=',$array->user_id)
                        ->where('report_id','=',$array->id)
                        ->count();
                    $singular["reporter_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reported_id"] = $array->reported_user_id;
                    $reporter = DB::table('users')
                        ->where('id','=',$array->reported_user_id)
                        ->first(); 
                    $singular["reported_warned"]=DB::table('warnings')
                        ->where('user_id','=',$array->reported_user_id)
                        ->where('report_id','=',$array->id)
                        ->count();
                    $singular["reported_type"]=$reporter->user_type;
                    $singular["reported_name"]=$reporter->last_name." ".$reporter->first_name;
                    $singular["reason"] = $array->user_reason;
                    $singular["description"] = $array->user_description;
                    switch($array->tbl) {
                        case "algorithms":
                            $singular["reportedType"] = "Algorithm";
                            $singular["linkName"] = DB::table('algorithms')
                                ->where('id','=',$array->reported_id)
                                ->get();
                            if(count($singular["linkName"])==0) {
                                $singular["linkName"] = "Algorithm was deleted!";
                                $singular["linkTo"] = "";
                                break;
                            }
                            
                            break;
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
                    $returnData["answered"][] = $singular;
                }
            }
            return Response::json(array("state"=>"success", "message"=>"Data successfully loaded", "data"=> $returnData));
        }
        return Response::json(array("state"=>"failure","message"=>"Insuficient priviledges."));
    }
    public function postWarn() {
    //data: {id: reports[reportsActionTarget].id, warn_id: reports[reportsActionTarget].reported_id},
        if(Auth::check() && Auth::user()->user_type >1) {
            $id = Request::input('id');
            $warn_id = Request::input('warn_id');
            $count = DB::table('warnings')
                ->where('user_id','=',$warn_id)
                ->where('report_id','=',$id)
                ->count();
            if($count == 0) {
                $time = date('Y-m-d H:i:s');
                    DB::insert('insert into warnings (user_id, report_id, created_at, updated_at) values (?, ?, ?, ?)', array(
                    $warn_id, 
                    $id,
                    $time,
                    $time));
                $fetchReport = DB::table('reports')
                    ->where('id',$id)
                    ->first();
                switch($fetchReport->tbl) {
                        case "algorithms":
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/posts/".$fetchReport->reported_id,
                                "Algorithm Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            break;
                        case "requests":
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/",
                                "Algorithm Request Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            break;
                        case "inline_algorithm_comments":
                            $getCommentData = DB::table('inline_algorithm_comments')
                                ->where('id','=',$fetchReport->reported_id)
                                ->first();
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/posts/".$getCommentData->algorithm_id,
                                "Line Comment Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            break;
                        case "algorithm_discussion":
                            $getCommentData = DB::table('algorithm_discussion')
                                ->where('id','=',$fetchReport->reported_id)
                                ->first();
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/posts/".$getCommentData->algorithm_id."#comment".$fetchReport->reported_id,
                                "Algorithm Comment Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                        case "algorithm_discussion_replies":
                            $getCommentData = DB::table('algorithm_discussion_replies')
                                ->where('id','=',$fetchReport->reported_id)
                                ->first();
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/posts/".$getCommentData->algorithm_id."#comment".$getCommentData->comment_id."_".$fetchReport->reported_id,
                                "Algorithm Reply Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            break;
                        case "profile_discussion":
                            $getCommentData = DB::table('profile_discussion')
                                ->where('id','=',$fetchReport->reported_id)
                                ->first();
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/profile/".$getCommentData->profile_id."#comment".$fetchReport->reported_id,
                                "Profile Comment Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            
                            break;
                        case "users":
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/profile/".$fetchReport->reported_id,
                                "Profile Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            
                        case "profile_discussion_replies":
                            $getCommentData = DB::table('profile_discussion_replies')
                                ->where('id','=',$fetchReport->reported_id)
                                ->first();
                            DB::insert('insert into notifications (user_id, who_said, url, title, text, what_was_said, seen, reference, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
                                $warn_id, 
                                Auth::user()->id,
                                "/profile/".$getCommentData->profile_id."#comment".$getCommentData->comment_id."_".$fetchReport->reported_id,
                                "Profile Reply Warn!",
                                "warned you.",
                                "",
                                FALSE,
                                "",
                                $time,
                                $time)
                            );
                            break;

                    }
                    
                return Response::json(array("state"=>"success","message"=>"User was issued a warning."));
            }
            return Response::json(array("state"=>"failure","message"=>"This warning already exists."));
        
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
    public function putSetanswered() {
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
    public function postUnbanuser() {
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
    public function postPromoteuser() {
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
    public function postDemoteuser() {
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
}

?>