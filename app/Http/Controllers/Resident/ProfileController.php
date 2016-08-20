<?php

namespace App\Http\Controllers\Resident;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->user;

        return view('TenantSync::resident/profile/index', compact('user'));
    }

    public function password()
    {
        if(! \Hash::check($this->input['current_password'], $this->user->password)) {
            return redirect()->back()->withErrors(["Current password doesn't match"]);
        }

        $this->validate($this->request, [
            'password' => 'required|confirmed|min:6',
        ]);

        $this->user->password = \Hash::make($this->input['password']);

        $this->user->save();

        return redirect()->back();
    }

    public function email()
    {
        $this->validate($this->request, [
                'email' => 'required|email|unique:users,email',
            ]);
        
        $this->user->email = $this->input['email'];
        
        $this->user->save();
        
        return redirect()->back();
    }

    public function name()
    {
        $this->validate($this->request, [
                'first_name' => 'required|max:30',
                'last_name' => 'required|max:30',
                'middle_initial' => 'required|max:1',
            ]);
        
        $this->user->first_name = $this->input['first_name'];
        $this->user->last_name = $this->input['last_name'];
        $this->user->middle_initial = $this->input['middle_initial'];
        
        $this->user->save();
        
        return redirect()->back();
    }
}