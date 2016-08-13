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

	public function displayResidents()
    {
        $devices = $this->user->devices;
        //$resident =  $devices[0]->residents;
        //return $resident[0]->user;
    	// Base resident view
		return view('TenantSync::resident.resident', compact('devices'));		
    }

    public function createResidentForm() {
        if(\Auth::user()->role ==  'landlord') {
            $devices=Device::where('user_id',$this->user->id)->get();
        }
        else if(\Auth::user()->role ==  'manager') {
            $devices=Device::where('id',$this->user->manager());
        } else {
            $devices="";
        }
        return view('TenantSync::resident/createresident', compact('devices'));
    }

}  
