<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\UserProperty;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\User;
use App\Http\Controllers\Auth;
use TenantSync\Mutators\PropertyMutator;use Response;

use DB;


class ResidentController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

	public function home()
    {
    	// Base resident view
		return view('TenantSync::resident.index');		
    }

	public function displayResidents($id)
    {
        if($id>0) {
            $device = Device::find($id);
            if($device->getCompany() != $this->user->company_id) {
                echo $device->getCompany();
                $devices = array();
            } else {
                $devices=array($device);
            }
        } else {
            $devices = $this->user->companyDevices();
        }
        //$resident =  $devices[0]->residents;
        //return $resident[0]->user;
    	// Base resident view
		return view('TenantSync::resident.resident', compact('devices'));		
    }

    public function createResidentForm() {
        $properties=Property::where('company_id',$this->user->company_id)->get();
        return view('TenantSync::resident/createresident', compact('properties'));
    }

    public function createResident() {
        $user = User::where('email',$this->input['email'])->get();
        if(count($user)>1) {
            // More than 1 user this is an issue
            $returnMessage="This email has multiple users please contact suppport";
        } else if(count($user)==1) {
            $userProperty=UserProperty::where('user_id',$user[0]->id)->where('device_id',$this->input['device_id'])->get();
            if(count($userProperty)>0) {
                // This user is already connected to this device
                $returnMessage="This user is already a resident of this unit.";
            } else {
                $this->input['user_id'] = $user[0]->id;
                UserProperty::create($this->input);
                $returnMessage="User added to unit.";
            }
        } else {
            $returnMessage="There was an error in processing.";
        }
        return $returnMessage;
    }
}  
