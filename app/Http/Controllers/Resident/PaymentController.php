<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TenantSync\Models\User;
use TenantSync\Models\Property;
use TenantSync\Models\UserProperty;
use App\Http\Controllers\Auth;
use TenantSync\Models\Transaction;
use Carbon\Carbon;
use DB;

use TenantSync\Models\Device;


class PaymentController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

	public function chooseLocation()
    {
        /*
    	$residents = DB::table('properties')
    	->leftJoin('user_property', 'user_property.property_id', '=', 'properties.id')
    	->where('user_property.user_id', '=', $this->user->id)
    	->get();
*/
        $residents = UserProperty::where('user_id', $this->user->id)->get();

        return view('TenantSync::resident/payments/payment1', compact('residents'));	
    }

    public function chooseAmount($id)
    {
        $paymentTypes = DB::table('payment_types')
        ->get();

        $paymentDetails = array(
            "property" => $id,
        );

        return view('TenantSync::resident/payments/payment2', compact('paymentTypes', 'paymentDetails'));    
    }

    public function choosePaymentMethod()
    {   
        $paymentTypes = DB::table('payment_types')
        ->get();
        $paymentAmount = 0;
        $paymentFor = "";
        $paymentFor=array();
        for($i=0;$i<count($paymentTypes);$i++) {    
            if(isset($this->input[str_replace(' ', '_', $paymentTypes[$i]->payment_type)])) {  
                if($this->input[str_replace(' ', '_', $paymentTypes[$i]->payment_type)]>0) {
                    $paymentAmount = $paymentAmount + $this->input[str_replace(' ', '_', $paymentTypes[$i]->payment_type)];
                    $paymentFor[$paymentTypes[$i]->payment_type] = $this->input[str_replace(' ', '_', $paymentTypes[$i]->payment_type)];
                }
            }
        }   

        $transactionFeeCredit = $paymentAmount * .0345;
        $transactionFeeBank = 3.45;

        if($transactionFeeCredit < .95) {
            $transactionFeeCredit = 0.95;
        }

        $paymentDisplay = "";
        foreach($paymentFor as $key => $value) {
            //do something with your $key and $value;
            $paymentDisplay = "(" . $key . " : " . money_format("$%i",$value) . ") " . $paymentDisplay;
        }

        $paymentDetails = array(
            "property" => $this->input['property'],
            "amount" => $paymentAmount,
            "paymentFor" => json_encode($paymentFor),
            "paymentDisplay" => $paymentDisplay,
        );

        return view('TenantSync::resident/payments/payment3', compact('paymentDetails'));    
    }

    public function accountInfo()
    {
        $paymentDetails = array(
            "property" => $this->input['property'],
            "amount" => $this->input['amount'],
            "paymentFor" => $this->input['paymentFor'],
            "payment_type" => $this->input['payment_type'],
        );
        return view('TenantSync::resident/payments/payment4', compact('paymentDetails'));    
    }

    public function reviewPayment()
    {
        $paymentDetails=$this->input;
        $unit=Device::find($this->input['property']);
        $paymentDecode = json_decode($this->input['paymentFor'],true);
        $paymentDisplay = "";
        
        foreach($paymentDecode as $key => $value) {
            //do something with your $key and $value;
            $paymentDisplay = "(" . $key . " : " . money_format("$%i",$value) . ") " . $paymentDisplay;
        } 

        $propertyDetails = array(
            "unit" => $unit->location,
            "property_address" => $unit->address(),
            "paymentDisplay" => $paymentDisplay,
        );
        return view('TenantSync::resident/payments/payment5', compact('paymentDetails', 'propertyDetails')); 
    }

    public function submitPayment() {
        \DB::beginTransaction();

        $amount = $this->input['amount'];
        $transactionFee = 0;

        $device = Device::find($this->input['property']);

        if($this->input['payment_type']=='credit') {
            $this->input['expiration'] = $this->input['month'] . $this->input['year'];
            $transactionFee=$this->input['amount']*0.0345;
            $this->input['amount'] = $amount + $transactionFee;  //This is the amount charged to USAEPAY
        } else if($this->input['payment_type']=='check') {
            $transactionFee=3.45;
            $this->input['amount'] = $amount + $transactionFee;  //This is the amount charged to USAEPAY
        }  

        $description = isset($this->input['description']) ? $this->input['description'] : $this->input['paymentFor'];

        $transaction = [
            'amount' => $amount,  //Using a different value than input['amount'] so this is stored in transaction table
            //'user_id' => $this->device->owner->id, 
            'user_id' => $device->user_id, 
            'payable_type' => 'device', 
            //'payable_id' => $this->device->id, 
            'payable_id' => $device->id, 
            'description' => $description, 
            'date' => Carbon::now()->toDateTimeString(), 
            'property_id' => $device->property_id,
            'payment_from_id' => $this->user->id,
            'status' => "processing",
            'payment_from_source' => 0,   //If 0 it is from web if >0 then it is from that device
            'payment_type' => $this->input['payment_type'],
            'transaction_fee' => $transactionFee,
        ];

        $payment = Transaction::create($transaction);
        $device = Device::find(74);
        $response = $device->payRent($amount+$transactionFee, array_merge($this->input, ['description' => $description]));

        if($response->Result == "Approved") {
            $payment->reference_number = $response->RefNum;
            
            $payment->save();

            \DB::commit();
        }
        else {
            \DB::rollback();
        }

        $paymentResponse = array(
            'RefNum' => $response->RefNum, 
            'Error' => $response->Error, 
            'Result' => $response->Result); 

        return view('TenantSync::resident/payments/submitpayment', compact('paymentResponse')); 
    }
}  
