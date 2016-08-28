<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TenantSync\Models\User;
use TenantSync\Models\Property;
use TenantSync\Models\Device;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\UserProperty;
use App\Http\Controllers\Auth;
use TenantSync\Models\Transaction;
use Carbon\Carbon;
use DB;
use Mail;


class UploadController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

    public function home() {
    	return view('TenantSync::resident/rentroll/readin'); 
    }

	public function rentRollSubmit()
    {
        
        // VALIDATION RULES
        /*
        $rules = array(
            'file' => 'image|max:3000',
        );
    
       // PASS THE INPUT AND RULES INTO THE VALIDATOR
        $validation = Validator::make($input, $rules);
 
        // CHECK GIVEN DATA IS VALID OR NOT
        if ($validation->fails()) {
            return Redirect::to('/')->with('message', $validation->errors->first());
        }
        */
        $file = $this->input['file'];
        // SET UPLOAD PATH
        $destinationPath = 'uploads';
        // GET THE FILE EXTENSION
        $extension = $file->getClientOriginalExtension();
        //$extension="csv";
        // RENAME THE UPLOAD WITH RANDOM NUMBER
        $fileName = "rentroll" . '.' . $extension;
        // MOVE THE UPLOADED FILES TO THE DESTINATION DIRECTORY
        $upload_success = $file->move($destinationPath, $fileName);
        
        // IF UPLOAD IS SUCCESSFUL SEND SUCCESS MESSAGE OTHERWISE SEND ERROR MESSAGE
        if (!$upload_success) {
        	return "it didn't work";
        }
/*
        $handle = \File::get('uploads/' . $fileName);

	        while (($data = fgetcsv($handle, 1000, ',')) !==FALSE)
	        {
	        	return $data;
	        }
	        */
		$handle = fopen('uploads/' . $fileName, "r");
		$startParse = false;
		$property=49;
		$changes=array();

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			if($csvLine[0]=="Building Totals") {
				$startParse = false;
			}
			if($startParse) {
			    $device = Device::where('property_id',$property)->where('location',$csvLine[0])->first();
			    if(count($device)>0) {
			    	if($device->resident_name!=$csvLine[2]) {
			    		$changes[$csvLine[0]."TENANT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Update tenant from " . $device->resident_name . " to " . $csvLine[2], 
            				);
			    		//"Unit: " . $csvLine[0] . " Will update tenant from " . $device->resident_name . " to " . $csvLine[2];
			    	}
			    	if($device->rent_amount!=$csvLine[3]) {
			    		$changes[$csvLine[0]."RENT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Update rent from " . $device->rent_amount . " to " . $csvLine[3], 
            				);
            				// "Unit: " . $csvLine[0] . " Will update rent from " . $device->rent_amount . " to " . $csvLine[3];
			    	}
			    } else {
			    	$changes[$csvLine[0]."NEWUNIT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Create new unit. Tenant: " . $csvLine[2] . " Rent: " . $csvLine[3], 
            				);
			    	//"New Unit: " . $csvLine[0] . " Tenant: " . $csvLine[2] . " Rent: " . $csvLine[3];
			    }
			}
			if($csvLine[0]=="Unit No") {
				$startParse = true;
			}
		}
        return view('TenantSync::resident/rentroll/reviewrentrollchanges', compact('changes')); 
    }

    public function makeRentRollChanges() {
    	$fileName='rentroll.csv';
		$handle = fopen('uploads/' . $fileName, "r");
		$startParse = false;
		$property=49;
		$changes="";
		$count=0;

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			$count++;
			if($csvLine[0]=="Building Totals") {
				$startParse = false;
			}
			if($startParse) {
			    $device = Device::where('property_id',$property)->where('location',$csvLine[0])->first();
			    if(count($device)>0) {
			    	if($device->resident_name!=$csvLine[2] && \Input::has($csvLine[0]."TENANT")) {
			    		$device->resident_name = $csvLine[2];
			    		$device->save();
			    	}
			    	if($device->rent_amount!=$csvLine[3] && \Input::has($csvLine[0]."RENT")) {
			    		$device->rent_amount=$csvLine[3];
			    		$device->save();
			    	}
			    } else if(\Input::has($csvLine[0]."NEWUNIT")) {
			    	$changes = $changes . "New Unit: " . $csvLine[0] . " Tenant: " . $csvLine[2] . " Rent: " . $csvLine[3] . "<br>";
			    	$newDevice = array(
				    		'location' => $csvLine[0], 
            				'rent_amount' => $csvLine[3], 
            				'resident_name' => $csvLine[2],
            				'property_id' => $property,
            				'user_id' => 0,
					        'token' => "123456",
					        'monthly_cost' => 0,
					        'late_fee' => 0,
					        'grace_period' =>0,
					        'vacant' => 0,
					        'alarm_id' => 0,
			    		);
			    	$device = Device::create($newDevice);
			        $device->save();
			    }
			}
			if($csvLine[0]=="Unit No") {
				$startParse = true;
			}
		}
        return view('TenantSync::resident/rentroll/updated');  	
    }
}