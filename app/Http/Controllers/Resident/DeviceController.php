<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\User;
use App\Http\Controllers\Auth;
use TenantSync\Mutators\PropertyMutator;use Response;


class DeviceController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

	public function home($id)
    {
        if($id>0) {
            $devices = Device::where('property_id',$id)->where('user_id',$this->user->id)->get();
        } else {
        	$devices = Device::where('user_id',$this->user->id)->get();
        }
		return view('TenantSync::resident.device', compact('devices'));
    }

    public function createDeviceForm() {
    	if(\Auth::user()->role ==  'landlord') {
        	$properties=Property::where('user_id',$this->user->id)->get();
        }
        else if(\Auth::user()->role ==  'manager') {
        	$properties=Property::where('id',$this->user->manager());
        } else {
        	$properties="";
        }
        return view('TenantSync::resident/devices/createdevice', compact('properties'));
    }

    public function createDevice() {
        if(\Auth::user()->role ==  'landlord') {
            $this->input['user_id']=$this->user->id;
        }
        else if(\Auth::user()->role ==  'manager') {
            $this->input['user_id']=$this->user->manager();
        }
        $this->input['token'] = "123456";
        $this->input['monthly_cost'] = 0;
        $this->input['late_fee'] = 0;
        $this->input['grace_period'] = 0;
        $this->input['vacant'] = 0;
        $this->input['alarm_id'] = 0;
        $payment = Device::create($this->input);
        return DeviceController::createDeviceForm();
    }

}  
