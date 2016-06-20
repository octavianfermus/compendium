<?php

use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class NotificationsController extends BaseController {
    
    public function putSeeall() {
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
    public function getAll() {
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

}

?>