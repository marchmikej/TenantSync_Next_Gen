<?php namespace App\Http\Controllers;

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

class HomeController extends Controller {

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

	public function sales()
	{
		$landlords = User::where(['role' => 'landlord'])->get();
		$devices = Device::all();
		return view('TenantSync::sales.index', compact('landlords', 'devices'));
	}

	public function landlord()
	{
		$manager = $this->user->manager();
		
		$devices = $this->user->devices->load(['property', 'alarm']);

		return view('TenantSync::manager.index', compact('devices', 'manager'));
	}

	public function manager()
	{
		$manager = $this->user->manager;

		return view('TenantSync::manager.index', compact('manager'));
	}

	public function test()
    {
    	return "test";
    	/*
    	$device=Device::find(73);
    	
    	if($device->alarm_id > 0) {
	    	OverdueUsage::create([
				'device_id' => $device->id, 
				'overdue_types_id' => 5
			]);
	    } 
	    $overdueUsage = OverdueUsage::where('device_id', '=', 73)->get();
	    //$overdueType = OverdueType::find($overdueType->overdue_types_id);
	    echo "device_id,location,address,city,state,zip,interaction,time\n</br>";
        for ($y = 0; $y < count($overdueUsage); $y++)
        {
          	$currentDevice=Device::find($overdueUsage[$y]->device_id);
          	$currentType=OverdueType::find($overdueUsage[$y]->overdue_type_id);
          	$content = $currentDevice["id"] . "," . $currentDevice["location"] . "," . $currentDevice->property->address . "," . $currentDevice->property->city . "," . $currentDevice->property->state . "," . $currentDevice->property->zip . "," . $currentType->overdue_description . "," . $overdueUsage[$y]->created_at . "\n";
          	echo $content . "</br>";
        }
    	return 'done';

    	/*  This is for dowloading a csv file 
		$devices = Device::all();
		$content ="device_id,location,property,city,state,zip,rent_amount,late_fee,rent_owed\n";

		if(count($devices) > 0) 
        {
            for ($y = 0; $y < count($devices); $y++)
            {
            	$currentDevice=$devices[$y];
            	$content = $content . $currentDevice["id"] . "," . $currentDevice["location"] . "," . $currentDevice->property->address . "," . $currentDevice->property->city . "," . $currentDevice->property->state . "," . $currentDevice->property->zip . "," . $currentDevice["rent_amount"] . "," . $currentDevice["late_fee"] . "," . $currentDevice->rentOwed() . "\n";
            }
        }

		// return an string as a file to the user
		return Response::make($content, '200', array(
    		'Content-Type' => 'application/octet-stream',
    		'Content-Disposition' => 'attachment; filename="TenantSyncDevices.csv"'
));   */
/*        $file= public_path(). "/images/app-debug.apk";

    	$headers = array(
        	'Content-Type: application/vnd.android.package-archive',
        );
        
        $file = \File::get($file);
    	
	    $response = \Response::make($file, 200);

	    return $response;
    	// return response()->download($file, 'app-debug.apk', $headers);
*/
    }

}  
