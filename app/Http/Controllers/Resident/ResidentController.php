<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\UserProperty;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\Transaction;
use TenantSync\Models\User;
use App\Http\Controllers\Auth;
use TenantSync\Mutators\PropertyMutator;use Response;

use Carbon\Carbon;
use DB;
use Mail;

class ResidentController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

	public function home()
    {
        if($this->user->company_id>0) {
            //Get payments from last 30 days
            $numberPayments = Transaction::where('company_id',$this->user->company_id)->where('date', '>', Carbon::now()->subDays(30))->count();
            $paymentSum = Transaction::where('company_id',$this->user->company_id)->where('date', '>', Carbon::now()->subDays(30))->sum('amount');
            $numberOfUnits = count($this->user->companyDevices());
            $autoPayments = Transaction::where('company_id',$this->user->company_id)->where('auto_payment_id','>',0)->where('date', '>', Carbon::now()->subDays(30))->count();
            $transactions = Transaction::where('company_id',$this->user->company_id)->get();
            //return $test;
            $overview=array(
                'number_payments' => $numberPayments,
                'payment_sum' => $paymentSum,
                'number_of_units' => $numberOfUnits,
                'auto_payments' => $autoPayments,
            );
        } else {
            $overview=array();
            $transactions=array();
        }
    	// Base resident view
		return view('TenantSync::resident.index', compact('overview', 'transactions'));		
    }

	public function displayResidents($id)
    {
        if($id>0) {
            $device = Device::find($id);
            if($device->getCompany() != $this->user->company_id) {
                echo $device->getCompany();
                $devices = array();
            } else {
                $devices=array($device);
            }
        } else {
            $devices = $this->user->companyDevices();
        }
        //$resident =  $devices[0]->residents;
        //return $resident[0]->user;
    	// Base resident view
		return view('TenantSync::resident.resident', compact('devices'));		
    }

    public function createResidentForm() {
        $properties=Property::where('company_id',$this->user->company_id)->get();
        return view('TenantSync::resident/createresident', compact('properties'));
    }

    public function createResident() {
            $this->validate($this->request, [
                'email' => 'required|email',
            ]);
        //Check if request is already pending
        $userProperty = UserProperty::
            where('device_id',$this->input['device_id'])
            ->where('status',$this->input['email'])
            ->first();
        $device=Device::where('id',$this->input['device_id'])->first();
        if(count($userProperty)>0) {
            // Request is already pending.  Get device information and resend email.
            $emailInfo = array(
                'email' => $this->input['email'],
                'address' => $device->address(),
                'user_property_id' => $userProperty->id,
                'device_id' => $this->input['device_id'],
                );
            if($device->getCompany() == $this->user->company_id) {
                Mail::send('emails.residentconfirm', ['emailInfo' => $emailInfo], function ($m) use ($emailInfo) {
                    $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                    $m->to($emailInfo['email'])->subject('Please Confirm Email');
                }); 
            } else {
                return "Error you do not own this unit";
            }

            $returnMessage="This request is already pending.  Email resent";
        } else {
            //Request has not yet been created
            $user = User::where('email',$this->input['email'])->first();
            //if count user==0 then we must create a new user_property and send request mail
            if(count($user)==0) {
                $returnMessage="This request was created and email sent";
            } else {
                $userProperty = UserProperty::
                    where('device_id',$this->input['device_id'])
                    ->where('user_id',$user->id)
                    ->where('status','active')
                    ->first();
                if(count($userProperty)>0) {
                    $message = array(
                        'message' => "This resident is already assigned to this property",
                    );
                    return view('TenantSync::resident/verify/messagelandlord', compact('message')); 
                } else {
                    $returnMessage="This request was created and email sent";
                }
            }
            if($device->getCompany() == $this->user->company_id) {
                $newUserProperty = array(
                    'user_id' => 0,
                    'device_id' => $this->input['device_id'],
                    'status' => $this->input['email'],
                );
                $userProperty = UserProperty::create($newUserProperty);
                $userProperty->save();
                $emailInfo = array(
                    'email' => $this->input['email'],
                    'address' => $device->address(),
                    'device_id' => $this->input['device_id'],
                );
                Mail::send('emails.residentconfirm', ['emailInfo' => $emailInfo], function ($m) use ($emailInfo) {
                    $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                    $m->to($emailInfo['email'])->subject('Please Confirm Email');
                }); 
            } else {
                $returnMessage =  "You do not own this unit";
            }
        }
        $message = array(
            'message' => $returnMessage,
        );
        return view('TenantSync::resident/verify/messagelandlord', compact('message')); 
/*
        $user = User::where('email',$this->input['email'])->get();

        if(count($user)>0) {
            // User already exists
            $userProperty = UserProperty::where('user_id',$user[0]->id)
                ->where('device_id',$this->input['device_id'])
                ->where('status','active')
                ->get();
            if(count($userProperty)>0) {
                $returnMessage="1This user is already a resident of this unit.";
            } else {

            }
        } else if(count($user)==1) {
            if(count($userProperty)>0 && $userProperty->status == 'active') {
                // This user is already connected to this device
                $returnMessage="2This user is already a resident of this unit.";
            } else if(count($userProperty)>0 && $userProperty->status == $this->input['email']) {
              Mail::send('emails.propertyreceipt', ['transactions' => $emailTransaction], function ($m) use ($user) {
                    $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                    $m->to($user->email)->subject('Payment Received');
                }); 
                $returnMessage="3User still pending.  Email resent";
            } else {
                $returnMessage="4Email sent to resident for confirmation";
            }
        } else {
            /*
            $this->input['user_id'] = $user[0]->id;
            $userProperty=UserProperty::create($this->input);
            return $userProperty; 
            Mail::send('emails.propertyreceipt', ['transactions' => $emailTransaction], function ($m) use ($user) {
                $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                $m->to($user->email)->subject('Payment Received'); 
            $returnMessage="5Email sent to resident for confirmation";
        } */
    }
}  
