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
        $this->validate($this->request, [
            'file' => 'required|mimes:csv,txt',
            'csvtype' => 'required',
        ]);
        
        $file = $this->input['file'];
        // SET UPLOAD PATH
        $destinationPath = 'uploads';
        // GET THE FILE EXTENSION
        $extension = $file->getClientOriginalExtension();
        //$extension="csv";
        // RENAME THE UPLOAD WITH RANDOM NUMBER
        $fileName = $this->user->id . '.csv';
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
		$changes=array();  //This is array passed for review
		$periodDate = "";  //This should populate as date report was run
		$propertyName = "";  //This is the name off of the CSV file
		$propertyAddress = "";  //This is the address off of the CSV file
		$property = array();
		$count = 0; //Line of csv

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			$count++;
			if($csvLine[0]=="Building Totals") {
				//Building Totals shows that units are done
				$startParse = false;
			}
			if($count==2) {
				$periodDate = $csvLine[3];			
			}			
			if($count==3) {
				$propertyName = $csvLine[0];  //This is the name off of the CSV file
				$propertyAddress = $csvLine[1];  //This is the address off of the CSV file		
				$property = Property::where('address',$csvLine[1])->where('company_id',$this->user->company_id)->first();
			}
			if($startParse) {
				///////////////////////////////
				// When $startParse=true     //
				// $csvLine[0] = UNIT        //
				// $csvLine[2] = Tenant      //
				// $csvLine[3] = Rent        //
				///////////////////////////////
				// Do not check device if creating a new property
				if(count($property)>0) {
				    $device = Device::where('property_id',$property->id)->where('location',$csvLine[0])->first();
				} else {
					$device=array();
				}
			    if(count($device)>0) {
			    	if($device->resident_name!=$csvLine[2]) {
			    		$changes[$csvLine[0]."TENANT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Update tenant from " . $device->resident_name . " to " . $csvLine[2], 
            				'Tenant' => $csvLine[2],
            				'Rent' => $csvLine[3],
            				);
			    		//"Unit: " . $csvLine[0] . " Will update tenant from " . $device->resident_name . " to " . $csvLine[2];
			    	}
			    	if($device->rent_amount!=$csvLine[3]) {
			    		$changes[$csvLine[0]."RENT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Update rent from " . $device->rent_amount . " to " . $csvLine[3], 
            				'Tenant' => $csvLine[2],
            				'Rent' => $csvLine[3],
            				);
            				// "Unit: " . $csvLine[0] . " Will update rent from " . $device->rent_amount . " to " . $csvLine[3];
			    	}
			    } else {
			    	$changes[$csvLine[0]."NEWUNIT"] = array(
				            'Unit' => $csvLine[0], 
            				'Action' => "Create new unit", 
            				'Tenant' => $csvLine[2],
            				'Rent' => $csvLine[3],
            				);
			    	//"New Unit: " . $csvLine[0] . " Tenant: " . $csvLine[2] . " Rent: " . $csvLine[3];
			    }
			}
			if($csvLine[0]=="Unit No") {
				$startParse = true;
			}
		}
		$updateDetails = array(
			'date' => $periodDate,
			'property_name' => $propertyName,
			'address' => $propertyAddress,
			'property' => $property,
		);
        return view('TenantSync::resident/rentroll/reviewrentrollchanges', compact('changes','updateDetails')); 
    }

    public function makeRentRollChanges() {
    	$fileName=$this->user->id . '.csv';
		$handle = fopen('uploads/' . $fileName, "r");
		$startParse = false;
		$periodDate = "";  //This should populate as date report was run
		$propertyName = "";  //This is the name off of the CSV file
		$propertyAddress = "";  //This is the address off of the CSV file
		$property = array();
		$count = 0; //Line of csv

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			$count++;
			if($csvLine[0]=="Building Totals") {
				$startParse = false;
			}
			if($count==2) {
				$periodDate = $csvLine[3];			
			}			
			if($count==3) {
				$propertyName = $csvLine[0];  //This is the name off of the CSV file
				$propertyAddress = $csvLine[1];  //This is the address off of the CSV file		
				$property = Property::where('address',$csvLine[1])->where('company_id',$this->user->company_id)->first();
				if(count($property)==0) {
			    	$newProperty = array(
				    		'user_id' => 0, 
            				'company_id' => $this->user->company_id, 
            				'address' => $csvLine[1],
			    		);
			    	$property = Property::create($newProperty);
			        $property->save();
				}
			}
			if($startParse) {
			    $device = Device::where('property_id',$property->id)->where('location',$csvLine[0])->first();
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

			    	$newDevice = array(
				    		'location' => $csvLine[0], 
            				'rent_amount' => $csvLine[3], 
            				'resident_name' => $csvLine[2],
            				'property_id' => $property->id,
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