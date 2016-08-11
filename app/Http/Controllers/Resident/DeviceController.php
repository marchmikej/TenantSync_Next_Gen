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

	public function home()
    {
    	$manager = $this->user->manager;
		return view('TenantSync::resident.device', compact('manager'));
    }

}  
