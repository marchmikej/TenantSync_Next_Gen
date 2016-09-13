<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TenantSync\Models\User;
use TenantSync\Models\Property;
use TenantSync\Models\AutoPayment;
use TenantSync\Models\UserProperty;
use App\Http\Controllers\Auth;
use TenantSync\Models\Transaction;
use Carbon\Carbon;
use DB;

use TenantSync\Models\Device;
use Mail;


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

        return view('TenantSync::resident/payments/chooselocation', compact('residents'));	
    }

    public function chooseAmount($id)
    {
        $paymentTypes = DB::table('payment_types')
        ->get();

        $paymentDetails = array(
            "property" => $id,
        );
        $device=Device::find($id);
        return view('TenantSync::resident/payments/chooseamount', compact('paymentTypes', 'device'));    
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

        return view('TenantSync::resident/payments/paymentmethod', compact('paymentDetails'));    
    }

    public function accountInfo()
    {
        $paymentDetails = array(
            "property" => $this->input['property'],
            "amount" => $this->input['amount'],
            "paymentFor" => $this->input['paymentFor'],
            "payment_type" => $this->input['payment_type'],
        );
        return view('TenantSync::resident/payments/accountinfo', compact('paymentDetails'));    
    }

    public function autoPayAccountInfo()
    {
        $paymentDetails = array(
            "property" => $this->input['property'],
            "amount" => $this->input['amount'],
            "paymentFor" => $this->input['paymentFor'],
            "payment_type" => $this->input['payment_type'],
        );
        return view('TenantSync::resident/payments/autopayaccountinfo', compact('paymentDetails'));    
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
        return view('TenantSync::resident/payments/reviewpayment', compact('paymentDetails', 'propertyDetails')); 
    }

    public function autoPayReviewPayment()
    {
        if(!empty($this->input['indefinite'])) {
            $this->input['NumLeft'] = -1;
        }        

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
        return view('TenantSync::resident/payments/autopayreviewpayment', compact('paymentDetails', 'propertyDetails')); 
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
            'status' => "Pending",
            'payment_from_source' => 0,   //If 0 it is from web if >0 then it is from that device
            'payment_type' => $this->input['payment_type'],
            'transaction_fee' => $transactionFee,
            'company_id' => $device->getCompany(),
        ];

        $payment = Transaction::create($transaction);
        $device = Device::find($device->id);
        $response = $device->payRent($amount+$transactionFee, array_merge($this->input, ['description' => $description]));

        if($response->Result == "Approved") {
            $payment->reference_number = $response->RefNum;
            
            $payment->save();

            \DB::commit();

            //Sending email to user for payment
            $emailTransaction=array($payment);
            $user=$payment->getUser();
            Mail::send('emails.propertyreceipt', ['transactions' => $emailTransaction], function ($m) use ($user) {
                $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');
                $m->to($user->email)->subject('Payment Received');
            });
        }
        else {
            \DB::rollback();
        }

        $paymentResponse = array(
            'RefNum' => $response->RefNum, 
            'Error' => $response->Error, 
            'Result' => $response->Result); 

        return view('TenantSync::resident/payments/paymentresponse', compact('paymentResponse')); 
    }

    public function autoPaySubmitPayment() {

        //Creating device/unit of the unit for this charge to be credited against
        $device = Device::find($this->input['property']);
        $amount = $this->input['amount'];
        $transactionFee = 0;

        //date format dd/mm/yyyy to yyyy-mm-dd
        //$autoMonth = substr($this->input['auto_date'],0,2);
        //$autoDay = substr($this->input['auto_date'],3,2);
        //$autoYear = substr($this->input['auto_date'],6,4);
        //$autoSend = $autoYear . "-" . $autoMonth . "-" . $autoDay;

        $autoSend = $this->input['auto_date'];

        // $payment array will hold payment information.  

        if($this->input['payment_type']=='credit') {
            $this->input['expiration'] = $this->input['month'] . $this->input['year'];
            $transactionFee=$this->input['amount']*0.0345;
            $this->input['amount'] = $amount + $transactionFee;  //This is the amount charged to USAEPAY
            $paymentArray = array(
                'CardNumber'=>$this->input['card_number'],
                'CardExpiration'=>$this->input['expiration'],
                'CardType'=>'', 
                'CardCode'=>$this->input['cvv2'],
                'AvsStreet'=>$this->input['address'],
                'AvsZip'=>$this->input['zip_code'],
                'MethodName'=>"Credit Card",
                'SecondarySort'=>0
            );
        } else if($this->input['payment_type']=='check') {
            $transactionFee=3.45;
            $this->input['amount'] = $amount + $transactionFee;  //This is the amount charged to USAEPAY
            $paymentArray = array(
                'Account'=>$this->input['account_number'],
                'AccountType'=>$this->input['AccountType'],
                'Routing'=>$this->input['routing_number'], 
                'RecordType'=>'',
                'MethodName'=>"Check",
                'SecondarySort'=>0
            );
        }  

        $CustomerData=array(
            'BillingAddress'=>array(
                'FirstName'=>$this->user->first_name,
                'LastName'=>$this->user->last_name,
                'Company'=>'',
                'Street'=>'',
                'Street2'=>'',
                'City'=>'',
                'State'=>'',
                'Zip'=>'',
                'Country'=>'',
                'Email'=>'',
                'Phone'=>'',
                'Fax'=>''),
            'PaymentMethods' => array(
                $paymentArray
                ),
            'CustomerID'=>$this->user->id,
            'Description'=>'',
            'Enabled'=>true,
            'Amount'=>$this->input['amount'],
            'Tax'=>'0',
            'Next'=>$autoSend,
            'Notes'=>$this->input['paymentFor'],
            'NumLeft'=>$this->input['NumLeft'],
            'OrderID'=>'',
            'ReceiptNote'=>'addCustomer test Created Charge',
            'Schedule'=>$this->input['Schedule'],
            'SendReceipt'=>false,
            'Source'=>'Recurring',
            'User'=>''
        );      
        // This will return the customer number of the created auto pay
        $response = $device->addCustomer($CustomerData);

        $autoPayment = [
            'user_id' => $this->user->id,
            'device_id' => $device->id,
            'customer_number' => $response,
            'schedule' => $this->input['Schedule'],
            'initial_date' => $this->input['auto_date'],
            'num_payments' => $this->input['NumLeft'],
            'amount' => $amount,
            'transaction_fee' => $transactionFee,
            'payment_type' => $this->input['payment_type'],
            'description' => $this->input['paymentFor'],
        ];  

        $newAutoPayment = AutoPayment::create($autoPayment);

        $newAutoPayment->save();

        $message = array(
            'message' => 'Auto Payment Scheduled.  Thank you!',
        );
        return view('TenantSync::resident/verify/message', compact('message')); 
    }

    public function viewAutoPayments() {
        if($this->user->company_id > 0) {
            $devices = $this->user->companyDevices();
            return view('TenantSync::resident/viewautopayslandlord', compact('devices'));  
        } else {
            $autoPayments = AutoPayment::where('user_id', $this->user->id)->get();
            return view('TenantSync::resident/viewautopays', compact('autoPayments'));    
        }
    }

    public function test() {

        $user = $this->user;
        //return view('TenantSync::resident/rentroll/readin', compact('autoPayments'));   
        
        $device=Device::find(99);
        $response = $device->findCustomer('6161652');

        $customerResponse = array(
            'CustNum' => $response->CustNum, 
            'CustomerID' => $response->CustomerID, 
            'Enabled' => $response->Enabled,
            'Schedule' => $response->Schedule,
            'NumLeft' => $response->NumLeft,
            'Next' => $response->Next,
            'OrderID' => $response->OrderID,
            'SendReceipt' => $response->SendReceipt,
            'Amount' => $response->Amount); 
       return $customerResponse;
        //6102639 customer num
        /*
        $paymentResponse = array(
            'CustNum' => $response->CustNum, 
            'CustomerID' => $response->CustomerID, 
            'Enabled' => $response->Enabled,
            'Schedule' => $response->Schedule,
            'NumLeft' => $response->NumLeft,
            'Next' => $response->Next,
            'OrderID' => $response->OrderID,
            'SendReceipt' => $response->SendReceipt,
            'Amount' => $response->Amount); 
            */
        /*
        //This will get the status of a past transaction
        $response = $device->getTransactionStatus('108850248');
        //$response = $device->addCustomer();
        $paymentResponse = array(
            'RefNum' => $response->RefNum, 
            'Error' => $response->Error, 
            'ErrorCode' => $response->ErrorCode,
            'AuthCode' => $response->AuthCode,
            'AuthAmount' => $response->AuthAmount,
            'Status' => $response->Status,
            'Result' => $response->Result); 
        */
/*
        $autoPayment=AutoPayment::where('customer_number','6114387')->first();

        $device=Device::find($autoPayment->device_id);

        $response = $device->getCustomerHistory($autoPayment->customer_number);

        $transactionSearchResult = array(
            'TransactionsMatched' => $response->TransactionsMatched, 
            'TransactionsReturned' => $response->TransactionsReturned, 
            'ErrorsCount' => $response->ErrorsCount,
            'DeclinesCount' => $response->DeclinesCount,
            'SalesCount' => $response->SalesCount,
            'CreditsCount' => $response->CreditsCount,
            'AuthOnlyCount' => $response->AuthOnlyCount,
            'VoidsCount' => $response->VoidsCount,
            'SalesAmount' => $response->SalesAmount); 

        //return $response->Transactions;
        foreach($response->Transactions as $transaction) {
            $currentTransaction = Transaction::where('reference_number',$transaction->Response->RefNum)->first();
            //If count==0 then transaction is not in database.  If greater than 0 move onto next transaction
            if(count($currentTransaction)==0) {
                $transactionResult = array(
                    'user_id' => $device->user_id,
                    'amount' => $autoPayment->amount,
                    'description' => $autoPayment->description,
                    'reference_number' => $transaction->Response->RefNum, 
                    'date' => $transaction->DateTime,
                    'payable_type' => "device",
                    'payable_id' => $device->id,
                    'payment_from_id' => $autoPayment->user_id,
                    'payment_from_source' => 0,
                    'status' => $transaction->Response->Status, 
                    'payment_type' => $autoPayment->payment_type,
                    'transaction_fee' => $autoPayment->transaction_fee,
                    'auto_payment_id' => $autoPayment->id
                    //'Result' => $transaction->Response->Result,
                    //'ErrorCode' => $transaction->Response->ErrorCode,
                    //'Error' => $transaction->Response->Error,
                    //'ResponseStatus' => $transaction->Response->Status
                    //'Amount' => $transaction->Details->Amount,
                ); 
                $createTransaction=Transaction::create($transactionResult);
                $emailTransaction=array($createTransaction);
                $user=$autoPayment->user();
                Mail::send('emails.propertyreceipt', ['transactions' => $emailTransaction], function ($m) use ($user) {
                    $m->from(env('SEND_EMAIL', 'admin@tenantsyncdev.com'), 'TenantSync');

                    $m->to($user->email)->subject('Payment Received');
                });
            }
        }
        return "end";
        */
    }
}  
