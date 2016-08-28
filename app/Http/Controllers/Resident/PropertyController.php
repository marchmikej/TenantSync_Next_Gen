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
    	$properties = Property::where('company_id',$this->user->company_id)->get();

		return view('TenantSync::resident/properties', compact('properties'));	
    }

    public function createPropertyForm() {
        return view('TenantSync::resident/properties/createproperty');
    }

    public function createProperty() {
        $this->input['user_id']=0;
        $this->input['company_id']=$this->user->company_id;
        $payment = Property::create($this->input);
        return PropertyController::home();
    }
}  