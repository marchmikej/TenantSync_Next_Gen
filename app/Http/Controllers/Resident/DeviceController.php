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
            $properties=Property::where('id',$id)->where('company_id',$this->user->company_id)->get();
            //$devices = Device::where('property_id',$id)->where('user_id',$this->user->id)->get();
        } else {
            $properties=Property::where('company_id',$this->user->company_id)->get();
        	//$devices = Device::where('user_id',$this->user->id)->get();
        }
		return view('TenantSync::resident.device', compact('properties'));
    }

    public function viewDevice($id) {
        $device = Device::find($id);
        \JavaScript::put([
            'device' => $device,
            'deviceMessages' => $device->messages,
        ]);
        return view('TenantSync::resident/devices/viewdevice', compact('device'));
    }

    public function createDeviceForm() {
    	$properties=Property::where('company_id',$this->user->company_id)->get();
        return view('TenantSync::resident/devices/createdevice', compact('properties'));
    }

    public function createDevice() {
        $this->input['user_id'] = 0;
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
