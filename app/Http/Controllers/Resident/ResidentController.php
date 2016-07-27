<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\User;
use TenantSync\Mutators\PropertyMutator;
use Response;

class ResidentController extends Controller {

	public function index(Auth $auth)
	{
		if($auth->check())
		{
			switch($auth->user()->role)
			{
				case 'admin':
					return $this->admin();
				case 'sales':
					return $this->sales();
				case 'landlord':
					return $this->landlord();
				case 'manager':
					return $this->manager();
			}
		}
		return view('auth/login');
	}

	public function test()
    {
    	return "test";
    }

}  
