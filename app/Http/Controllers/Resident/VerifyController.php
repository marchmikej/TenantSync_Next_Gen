<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\UserProperty;
use TenantSync\Models\User;
use TenantSync\Mutators\PropertyMutator;
use Response;
use Auth;


class VerifyController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
    }

	public function resident($id,$email)
    {
        $userProperty = UserProperty::
            where('device_id',$id)
            ->where('status',$email)
            ->first();

        if(count($userProperty)>0) {
            $user=User::where('email',$email)->first();
            $device=Device::find($id);
            if(count($user)>0) {
                $verifyInfo=array(
                    'email' => $email,
                    'device_id' => $id,
                    'first_name' => $user->first_name,
                    'middle_initial' => $user->middle_initial,
                    'last_name' => $user->last_name,
                    'address' => $device->address(),
                );
            } else {
                $verifyInfo=array(
                    'email' => $email,
                    'device_id' => $id,
                    'first_name' => "",
                    'middle_initial' => "",
                    'last_name' => "",
                    'address' => $device->address(),
                );
            }
            return view('TenantSync::resident/verify/newuser', compact('verifyInfo'));   
        } else {
            $message = array(
                'message' => "This request is no longer pending",
            );
            return view('TenantSync::resident/verify/message', compact('message')); 
        }
    }

    public function residentVerify($id,$email)
    {
        $user=User::where('email',$email)->first();
        if(count($user)>0) {
            $this->validate($this->request, [
                'password' => 'required|confirmed|min:6',
            ]);
        } else {
            if($this->input['email']==$email) {
                $this->validate($this->request, [
                    'first_name' => 'required|max:30',
                    'last_name' => 'required|max:30',
                    'middle_initial' => 'required|max:1',
                    'email' => 'required|email|max:255|unique:users',
                    'password' => 'required|confirmed|min:6',
                ]);
            } else {
                $message = array(
                    'message' => 'Email address does not match request.',
                );
                return view('TenantSync::resident/verify/message', compact('message')); 
            }

        }
        $message="";
        $userProperty = UserProperty::
            where('device_id',$id)
            ->where('status',$email)
            ->first();
        if(count($userProperty)>0) {
            //If user exists already authenticate otherwise create new user
            if(count($user)>0) {
                if (Auth::attempt(['email' => $email, 'password' => $this->input['password']])) {
                    $userProperty->status='active';
                    $userProperty->user_id=$user->id;
                    $userProperty->save();
                    $message = "Email validated!";
                } else {
                    return back()->withErrors('Password Incorrect')->withInput();
                }
            } else {
                $user=User::create([
                    'first_name' => $this->input['first_name'],
                    'last_name' => $this->input['last_name'],
                    'middle_initial' =>$this->input['middle_initial'],
                    'email' => $email,
                    'password' => bcrypt($this->input['password']),
                ]);
                $userProperty->status='active';
                $userProperty->user_id=$user->id;
                $userProperty->save();
                $message="Login created and email verified.";
            }
        } else {
            $message = "This validation request is no longer outstanding";
        }
        $message = array(
            'message' => $message,
        );
        return view('TenantSync::resident/verify/message', compact('message')); 
    }    
}  