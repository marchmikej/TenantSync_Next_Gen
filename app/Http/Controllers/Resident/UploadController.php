<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TenantSync\Models\User;
use TenantSync\Models\Property;
use TenantSync\Models\Device;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\AdditionalCharge;
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
		$currentUnit="none";

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			$count++;
			$itemCount=count($csvLine);
			if($csvLine[0]=="Building Totals") {
				//Building Totals shows that units are done
				$startParse = false;
			}
			if($csvLine[0]=='For the Period' && $count==2 && $itemCount>3) {
				$periodDate = $csvLine[3];			
			}			
			if($count==3) {
				$propertyName = $csvLine[0];  //This is the name off of the CSV file
				$propertyAddress = $csvLine[1];  //This is the address off of the CSV file		
				$property = Property::where('address',$csvLine[1])->where('company_id',$this->user->company_id)->first();
			}
			if($startParse && $itemCount > 11) {
				///////////////////////////////
				// When $startParse=true     //
				// $csvLine[0] = UNIT        //
				// $csvLine[2] = Tenant      //
				// $csvLine[3] = Rent        //
				///////////////////////////////
				// Do not check device if creating a new property
				$currentUnit = $csvLine[0];
				if(count($property)>0) {
				    $device = Device::where('property_id',$property->id)->where('location',$currentUnit)->first();
				} else {
					$device=array();
				}
			    if(count($device)>0) {
			    	if($device->resident_name!=$csvLine[2]) {
			    		$changes[$currentUnit."TENANT"] = array(
				            'Unit' => $currentUnit, 
            				'Action' => "Update tenant from " . $device->resident_name . " to " . $csvLine[2], 
            				'Tenant' => $csvLine[2],
            				'Cost' => '',
            				);
			    		//"Unit: " . $csvLine[0] . " Will update tenant from " . $device->resident_name . " to " . $csvLine[2];
			    	}
			    	if($device->rent_amount!=str_replace(' ', '_', $csvLine[3])) {
			    		if(is_numeric(str_replace(',', '',$csvLine[3]))) {
				    		$changes[$currentUnit."RENT"] = array(
					            'Unit' => $currentUnit, 
	            				'Action' => "Update rent from " . $device->rent_amount . " to " . $csvLine[3], 
	            				'Tenant' => $csvLine[2],
	            				'Cost' => str_replace(',', '', $csvLine[3]),
	            				);
				    	} else {
				    		$wholeLine="";
							foreach($csvLine as $line) {
								$wholeLine =  $wholeLine . $line . ",";
							}
				    		$changes[$currentUnit."RENT"] = array(
						        'Unit' => $wholeLine, 
			            		'Action' => "Not proper format", 
			            		'Tenant' => '',
			           			'Cost' => '',
			           			'read' => false,
         					); 
				    	}
            				// "Unit: " . $csvLine[0] . " Will update rent from " . $device->rent_amount . " to " . $csvLine[3];
			    	}
			    } else {
			    	$changes[$currentUnit."NEWUNIT"] = array(
				            'Unit' => $currentUnit, 
            				'Action' => "Create new unit", 
            				'Tenant' => $csvLine[2],
            				'Cost' => '',
            				'read' => true,
            				);
			    	if(is_numeric(str_replace(',', '',$csvLine[3]))) {
				    	$changes[$currentUnit."RENT"] = array(
				            'Unit' => $currentUnit, 
            				'Action' => "Assign monthly rent", 
            				'Tenant' => '',
            				'Cost' => str_replace(',', '', $csvLine[3]),
            				'read' => true,
	            		);
			    	}
			    	else {
			    		$wholeLine="";
						foreach($csvLine as $line) {
							$wholeLine =  $wholeLine . $line . ",";
						}
			    		$changes[$currentUnit."RENT"] = array(
					        'Unit' => $wholeLine, 
		            		'Action' => "Not proper format", 
		            		'Tenant' => '',
		           			'Cost' => '',
		           			'read' => false,
     					); 
				    }
			    	//"New Unit: " . $csvLine[0] . " Tenant: " . $csvLine[2] . " Rent: " . $csvLine[3];
			    }
			    // Check if there are additional charges on this line
			    if(is_numeric(str_replace(',', '', $csvLine[6])) && strlen($csvLine[5])) {
			    	$changes[$currentUnit.str_replace(' ', '_',$csvLine[5])] = array(
			            'Unit' => $currentUnit, 
        				'Action' => $csvLine[5], 
        				'Tenant' => '',
        				'Cost' => str_replace(',', '', $csvLine[6]),
        				'read' => true,
        			);	
			    }
			} else if($startParse && $itemCount > 2) {
				// This is most likely a continuation of the previous line.  Most likely additional charges.
				//echo $csvLine[0] . ", " . $csvLine[1] . ", " . $csvLine[2] . "<br>";
				if(is_numeric(str_replace(',', '', $csvLine[2]))) {
			    	$changes[$currentUnit.str_replace(' ', '_',$csvLine[1])] = array(
			            'Unit' => $currentUnit, 
	    				'Action' => $csvLine[1], 
	    				'Tenant' => '',
	    				'Cost' => str_replace(',', '', $csvLine[2]),
	    				'read' => true,
	    			);
			    } else {
		    		$wholeLine="";
					foreach($csvLine as $line) {
						$wholeLine =  $wholeLine . $line . ",";
					}
		    		$changes[$currentUnit.str_replace(' ', '_',$csvLine[1])] = array(
				        'Unit' => $wholeLine, 
	            		'Action' => "Not proper format", 
	            		'Tenant' => '',
	           			'Cost' => '',
	           			'read' => false,
 					); 
			    }
			} else if($startParse && $itemCount > 0) {
				$wholeLine="";
				foreach($csvLine as $line) {
					$wholeLine =  $wholeLine . $line . ",";
				}
			    $changes[$csvLine[0]."NEWUNIT"] = array(
			        'Unit' => $wholeLine, 
            		'Action' => "Not proper format", 
            		'Tenant' => '',
           			'Cost' => '',
           			'read' => false,
         		); 
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
		$currentUnit="none";
		$currentDevice="0";

		while ($csvLine = fgetcsv($handle, 1000, ",")) {
			$count++;
			$itemCount=count($csvLine);

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
			if($startParse && $itemCount > 11) {
				$currentUnit = $csvLine[0];
			    $device = Device::where('property_id',$property->id)->where('location',$csvLine[0])->first();
			    $currentDevice = $device;
			    if(count($device)>0) {
			    	DB::table('additional_charges')->where('device_id', $device->id)->delete();
			    	if($device->resident_name!=$csvLine[2] && \Input::has($csvLine[0]."TENANT")) {
			    		$device->resident_name = $csvLine[2];
			    		$device->save();
			    	}
			    	if($device->rent_amount!=$csvLine[3]) {
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
		    	// Check if there are additional charges on this line
			    if(is_numeric($csvLine[6]) && strlen($csvLine[5]) && \Input::has($csvLine[0].str_replace(' ', '_',$csvLine[5]))) {
			    	$additionalCharge = new AdditionalCharge;
			        $additionalCharge->payment_type = $csvLine[5];
			        $additionalCharge->device_id = $device->id;
			        $additionalCharge->amount = $csvLine[6];
			        $additionalCharge->save();
			    }
			} else if($startParse && $itemCount > 2 && \Input::has($currentUnit.str_replace(' ', '_',$csvLine[1]))) {
				// This is most likely a continuation of the previous line.  Most likely additional charges.
				//echo $csvLine[0] . ", " . $csvLine[1] . ", " . $csvLine[2] . "<br>";
				$additionalCharge = new AdditionalCharge;
		        $additionalCharge->payment_type = $csvLine[1];
		        $additionalCharge->device_id = $currentDevice->id;
		        $additionalCharge->amount = $csvLine[2];
		        $additionalCharge->save();

			}
			if($csvLine[0]=="Unit No") {
				$startParse = true;
			}
		}
        return view('TenantSync::resident/rentroll/updated');  	
    }
}