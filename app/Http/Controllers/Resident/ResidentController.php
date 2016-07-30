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

class ResidentController extends Controller {
    
public function __construct()
    {
        $this->middleware('auth');
    }

	public function home()
    {
	return view('TenantSync::resident.index');		
    }

}  
