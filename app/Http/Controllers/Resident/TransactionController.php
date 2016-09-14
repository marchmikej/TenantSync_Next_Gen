<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\Manager;
use TenantSync\Models\Transaction;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\User;
use App\Http\Controllers\Auth;
use TenantSync\Mutators\PropertyMutator;use Response;

use TenantSync\Billing\RentPaymentGateway;
use TenantSync\Models\RecurringTransaction;
use TenantSync\Mutators\TransactionMutator;
use App\Http\Requests\CreateTransactionRequest;

class TransactionController extends Controller {
    
public function __construct(TransactionMutator $transactionMutator)
    {
    	parent::__construct();
    	$this->transactionMutator = $transactionMutator;
        $this->middleware('auth');
    }

    public function home() {
        $searchArray = array(
            'search' => 'NOSEARCH',
            'start_date' => 'NODATE',
            'end_date' => 'NODATE'
        ); 
        $transactions = Transaction::where('company_id',$this->user->company_id)->orderBy('date', 'desc')->get();   
        return view('TenantSync::resident.transactions.index', compact('transactions', 'searchArray'));    
    }

	public function homeSearch()
    {  
        $searchArray = array(
            'search' => 'NOSEARCH',
            'start_date' => 'NODATE',
            'end_date' => 'NODATE'
        ); 
        if($this->input['start_date']!=NULL && $this->input['end_date']!=NULL) {
            $transactions = Transaction::where('company_id',$this->user->company_id)
                ->where('date','>=',$this->input['start_date'])
                ->where('date','<=',$this->input['end_date'])
                ->orderBy('date', 'desc')->get();
            $searchArray['start_date'] = $this->input['start_date'];
            $searchArray['end_date'] = $this->input['end_date'];
        } else if($this->input['start_date']!=NULL) {
            $transactions = Transaction::where('company_id',$this->user->company_id)
                ->where('date','>=',$this->input['start_date'])
                ->orderBy('date', 'desc')->get();
            $searchArray['start_date'] = $this->input['start_date'];
        } else if($this->input['end_date']!=NULL) {
            $transactions = Transaction::where('company_id',$this->user->company_id)
                ->where('date','<=',$this->input['end_date'])
                ->orderBy('date', 'desc')->get();
            $searchArray['end_date'] = $this->input['end_date'];
        } else {
            $transactions = Transaction::where('company_id',$this->user->company_id)->orderBy('date', 'desc')->get();   
        }
        if($this->input['search']!=NULL) {
            $searchArray['search'] = $this->input['search'];
        }
		return view('TenantSync::resident.transactions.index', compact('transactions', 'searchArray'));	
    }

    public function all()
    {
        return $this->user->company()->transactions;
    }
}  