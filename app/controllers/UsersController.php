<?php
 
class UsersController extends BaseController {
    public function getRegister() {
        $this->layout->content = View::make('landing');
    }
    public function getLogin() {
        $this->layout->content = View::make('landing');
    }
    public function getLogout() {
        Session::flush();
        return Redirect::to('/');
    }
    public function postCreate() {
        $validator = Validator::make(Input::all(), User::$rules);
        
        if ($validator->passes()) {
            $user = new User;
            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save();
            if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
                return Redirect::to('/');
            }
        } else {
            //Failure
            return Redirect::to('/')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
        }
    }
    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
            return Redirect::to('/');
        } else {
            //Failure
            return Redirect::to('/')
                ->withInput();
        }     
    }
}

?>