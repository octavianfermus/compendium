<?php

class MessagingController extends BaseController {
    
    public function getMessagehistory() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $timestamp = Request::input('timestamp');
            $id = Request::input('id');
            $returnData = array();
            $timestampcheck = DB::table('private_messages')
                ->where('from_id', '=', Auth::user()->id)
                ->orWhere(function($query)
                {
                    $query->where('to_id', '=', Auth::user()->id);
                })
                ->count();
            $found = DB::table('users')
                ->where('id','=',$id);
            if($found->count() == 0 || strlen($id) == 0) {
                $returnData["state"] = 'failure';
                $returnData["message"] = 'There is no user with the provided id.';
                $returnData["timestamp"] = '';
                return Response::json($returnData);
            } else {
                 $found = DB::table('users')
                    ->where('id','=',$id)
                    ->first();
                $returnData["state"] = 'success';
                $returnData["message"] = 'User found. Returning message history if timestamp is different.';
                $returnData["talkingTo"] = $found->last_name." ".$found->first_name;
                    
            }
            
            if($timestampcheck > 0) {
                $returnData["timestamp"] = DB::table('private_messages')
                    ->where('from_id', '=', Auth::user()->id)
                    ->orWhere(function($query)
                    {
                        $query->where('to_id', '=', Auth::user()->id);
                    })
                     ->orderBy('updated_at', 'desc')
                    ->first()
                    ->updated_at;
                
                if($timestamp !== $returnData["timestamp"]) {
                    
                   
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
                        if($array->from_id == Auth::user()->id) {
                            $found = DB::table('users')
                                ->where('id','=',$array->to_id)
                                ->count();
                        } else {
                            $found = DB::table('users')
                                ->where('id','=',$array->from_id)
                                ->count();
                        }
                        if($found > 0) {
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
                    }
                    $returnData["history"]=$history;
                    $crumbs = array();
                    $crumb_unparsed = DB::table('private_messages')
                        ->where('from_id', '=', Auth::user()->id)
                        ->orWhere(function($query)
                        {
                            $query->where('to_id', '=', Auth::user()->id);
                        })
                         ->orderBy('created_at', 'desc')
                        ->get();
                    $added = array();
                    foreach($crumb_unparsed as $array) {
                        $singular = array();
                        if($array->to_id == Auth::user()->id) {
                            $found = DB::table('users')
                                ->where('id','=',$array->to_id)
                                ->count();
                            if(!in_array($array->from_id,$added) && $found > 0) {
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
                            $found = DB::table('users')
                                ->where('id','=',$array->to_id)
                                ->count();
                            if(!in_array($array->to_id,$added)  && $found > 0) {
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
                    
                } else {
                    return Response::json($returnData);
                }
            } else {
                $returnData["timestamp"] = "";
            }
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive messages.'));
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
    public function postMessagecrumb() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            
            $timestamp = Request::input('timestamp');
            $timestampcheck = DB::table('private_messages')
                ->where('from_id', '=', Auth::user()->id)
                ->orWhere(function($query)
                {
                    $query->where('to_id', '=', Auth::user()->id);
                })
                ->count();
            $returnData = array();
            
            if($timestampcheck > 0) {
                $returnData["timestamp"] = DB::table('private_messages')
                    ->where('from_id', '=', Auth::user()->id)
                    ->orWhere(function($query)
                    {
                        $query->where('to_id', '=', Auth::user()->id);
                    })
                     ->orderBy('updated_at', 'desc')
                    ->first()
                    ->updated_at;
                if($returnData["timestamp"] != $timestamp) {
                    $crumbs = array();
                    $crumb_unparsed = DB::table('private_messages')
                        ->where('from_id', '=', Auth::user()->id)
                        ->orWhere(function($query)
                        {
                            $query->where('to_id', '=', Auth::user()->id);
                        })
                         ->orderBy('created_at', 'desc')
                        ->get();
                    $added = array();
                    foreach($crumb_unparsed as $array) {
                        $singular = array();
                        if($array->to_id == Auth::user()->id) {
                            $found = DB::table('users')
                                ->where('id','=',$array->to_id)
                                ->count();
                            if(!in_array($array->from_id,$added) && $found > 0) {
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
                            $found = DB::table('users')
                                ->where('id','=',$array->to_id)
                                ->count();
                            if(!in_array($array->to_id,$added)  && $found > 0) {

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
                } else {
                    $returnData["timestamp"] = $timestamp;
                }
                
                
                return Response::json($returnData);
            }
            
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive messages.'));
        }
        
    }
    
    public function getGroups() {
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
                    ->where('accepted','=',1)
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
    public function getGroupcrumb() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $returnData = array();
            $timestamp = Request::input('timestamp');
            $timestampcheck = DB::table('group_members')
                ->where('member_id','=',Auth::user()->id)
                ->count();
            
            if($timestampcheck > 0) {
                
                $returnData["timestamp"] = DB::table('group_members')
                    ->where('member_id',Auth::user()->id)
                    ->orderBy('updated_at','desc')
                    ->first()
                    ->updated_at;
                if($timestamp != $returnData["timestamp"]) {
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
                }
            }
            
           
            return Response::json($returnData);
        } else {
            return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive this data.'));
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
                    ->where('accepted','=',1)
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
    public function postJoingroup() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $time = date('Y-m-d H:i:s');
            $getType = DB::table('groups')
                ->where('id','=',$id)
                ->first()
                ->private;
            $count = DB::table('group_members')
                ->where('member_id','=',Auth::user()->id)
                ->where('group_id','=',$id)
                ->count();
            if($count == 0) {
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
            }
                $ownData = DB::table('group_members')
                    ->where('group_id','=',$id)
                    ->where('member_id','=',Auth::user()->id)
                    ->first();
            return Response::json($ownData);    
        }
    }
    public function deleteCancelrequest() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $count = DB::table('group_members')
                ->where('group_id',$id)
                ->where('member_id',Auth::user()->id)
                ->count();
            if($count!=0) {
                DB::delete('delete from group_members where group_id = ? and member_id = ? and accepted = 0', array($id, Auth::user()->id));
                return Response::json(array('state' => 'success', 'message'=>'canceled request'));
            }
            return Response::json(array('state' => 'failure', 'message'=>'request not found'));
        }
    }
    public function getGroupinitialdata() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $id = Request::input('id');
            $timestamp = Request::input('timestamp');
            $returnData = array();
            
            $returnData["timestamp"] = DB::table('group_messages')
                ->where('group_id', '=', $id)
                ->count();
            $found = DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('member_id','=',Auth::user()->id)
                ->where('accepted','=',1)
                ->count();
            if($returnData["timestamp"] > 0) {
                $returnData["timestamp"] = DB::table('group_messages')
                ->where('group_id', '=', $id)
                ->orderBy('updated_at', 'desc')
                ->first()
                ->updated_at;
                if($timestamp != $returnData["timestamp"]) {
                    if($found == 0) {
                        $returnData["message"] = 'Group found. No messages.';
                    } else {
                        $returnData["state"] = 'success';
                        $returnData["message"] = 'Group found. Returning message history';
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
            } 
            if($timestamp=="") {
            $found = DB::table('groups')
                ->where('id','=',$id)
                ->count();
            if($found == 0) {
                $returnData["state"] = 'failure';
                $returnData["message"] = 'No group with the provided id exists.';
            } else {
                $returnData["state"] = 'success';
                $found = DB::table('groups')
                    ->where('id','=',$id)
                    ->first();
                $returnData["requested"] = DB::table('group_members')
                    ->where('accepted','=',0)
                    ->where('member_id','=',Auth::user()->id)
                    ->where('group_id','=',$id)
                    ->count();
                $returnData["groupName"] = $found->group_name;
                $returnData["description"] = $found->description;
                $returnData["privateGroup"] = $found->private;
                $returnData["leader_id"] = $found->leader;
                if($found->leader == Auth::user()->id) {
                    $returnData["leader_me"] = TRUE;
                } else {
                    $returnData["leader_me"] = FALSE;
                }
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
                 $returnData["me"] = Auth::user()->id;
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
            }}
            return Response::json($returnData);
        }
        return Response::json(array('state' => 'failure', 'message'=>'You must be logged in to receive messages.'));
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
    public function postKickfromgroup() {
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
}
?>