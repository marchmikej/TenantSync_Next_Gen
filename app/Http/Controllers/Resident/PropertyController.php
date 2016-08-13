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

use TenantSync\Models\Manager;
use TenantSync\Mutators\DeviceMutator;
use App\Http\Controllers\Manager\ManagerBaseController;


class PropertyController extends ManagerBaseController {
    
public function __construct()
    {
        $this->middleware('auth');
    	parent::__construct();
    }

	public function home()
    {
    	$devices = $this->user->manager->devices()->toArray();

		$manager = $this->user->manager;

		return view('TenantSync::resident/properties', compact('devices', 'manager'));	
    }

    public function createPropertyForm() {
        return view('TenantSync::resident/properties/createproperty');
    }

    public function createProperty() {
        if(\Auth::user()->role ==  'landlord') {
            $this->input['user_id']=$this->user->id;
        }
        else if(\Auth::user()->role ==  'manager') {
            $this->input->user_id=$this->user->manager();
        }
        $payment = Property::create($this->input);
        return PropertyController::home();
    }
}  