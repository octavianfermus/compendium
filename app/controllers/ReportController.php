<?php

class ReportController extends Controller {

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
