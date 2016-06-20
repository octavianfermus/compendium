<?php

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class RequestsController extends BaseController {
    
    public function getAll() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $allRequests = Request::input('all_requests');
            if($allRequests == true) {
                $unparsedData = DB::table('algorithm_requests')
                    ->get();
            } else {
                $unparsedData = DB::table('algorithm_requests')
                    ->where('user_id','!=',Auth::user()->id)
                    ->get();
            }
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
                $found = DB::table('algorithm_request_votes')
                    ->where('request_id','=',$array->id)
                    ->where('user_id','=',Auth::user()->id)
                    ->count();
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
    public function putVote() {
        $request_id = Request::input('id');
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
        
        return Response::json(array('state' => 'failure', 'message'=>'Request not found.'));
       
    }
    public function postSubmit() {
        if(Auth::check() && Auth::user()->user_type > 0) {
            $algorithm_name = Input::get('algorithm_name');
            $algorithm_description = Input::get('algorithm_description');
            $language = Input::get('language');
            if($algorithm_name =="" || $algorithm_description =="" || $language =="") {
                return Response::json(array('state' => 'failure', 'message'=>'All fields must be submited.'));
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
            return Response::json(array('state' => 'success', 'message'=>'Algorithm request successfuly submitted.'));
        } 
        return Response::json(array('state' => 'failure', 'message'=>'Request not found.'));
    }
    
}

?>