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
    public function getProfile() {
        if(Auth::check()) {
            return View::make('profile');
        } else {
            return View::make('landing');
        }
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
                    return Redirect::to('users/profile')->withErrors($errors)->withInput();
                } else {
                    DB::table('users')
                        ->where('id', Auth::user()->id)
                        ->update($updates);
                    return Redirect::to('users/profile');
                }
            } else {
                return Redirect::to('users/profile');
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
            DB::delete('delete from algorithms where id = ? and user_id = ?', array($algorithmId, Auth::user()->id));
            return Response::json(array('state' => 'success', 'message'=>'Algorithm successfuly deleted.'));
        }
        return Response::json(array('state' => 'failure', 'message'=>'Algorithm not found.'));
    }
    public function postPushalgorithm() {
        $input = Input::all();
        DB::insert('insert into algorithms (user_id, name, description, language,original_link, template, content) values (?, ?, ?, ?, ?, ?, ?)', array(
            Auth::user()->id, 
            Input::get('algorithm_name'), 
            Input::get('algorithm_description'), 
            Input::get('language'), 
            Input::get('original_link'), 
            Input::get('template'), 
            Input::get('algorithm_code'))
                  );
        return Redirect::to('/')->withErrors(['Algorithm successfully added.']);
    }
    public function postEditalgorithm() {
        $input = Input::all();
        DB::update('update algorithms set name = ?, language = ?, description = ?, template = ?, original_link = ?, content = ? where id = ?', array(
            Input::get('algorithm_name'), 
            Input::get('language'), 
            Input::get('algorithm_description'), 
            Input::get('template'), 
            Input::get('original_link'), 
            Input::get('algorithm_code'),
            Input::get('algorithm_id')));
        if(Input::get('template') == 0) {
            return Redirect::to('/')->withErrors("Algorithm successfuly published.");
        } else {
            return Redirect::to('/users/editalgorithm/'.Input::get('algorithm_id'))->withErrors("Algorithm successfuly updated.");
        }
    }
    public function postSubmitrequest() {
        if(Auth::check()) {
            $algorithm_name = Input::get('algorithm_name');
            $algorithmdescription = Input::get('algorithm_description');
            $language = Input::get('language');
            if($algorithm_name =="" || $algorithmdescription =="" || $language =="") {
                return Redirect::to('/')->withErrors("All request fields must be completed.")->withInput();
            }
            DB::insert('insert into algorithm_requests (user_id, name, description, language, upvotes) values (?, ?, ?, ?, ?)', array(
            Auth::user()->id, 
                Input::get('algorithm_name'), 
                Input::get('algorithm_description'), 
                Input::get('language')),
                1
            );
            return Redirect::to('/')->withErrors("Algorithm request successfully submitted.");
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
                    ->count();
        if($found==1) {
            DB::update('update algorithms set template = 0 where user_id = ? and id = ?', array(Auth::user()->id, $algorithmId));
            return Response::json(array('state' => 'success', 'message'=>'Algorithm successfuly published.'));
        }
        return Response::json(array('state' => 'failure', 'message'=>'Algorithm not found.'));
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
            return Response::json($returnData);
        }
        return Response::json(array('data'=>$found));
       
    }
}

?>