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

	public function home()
    {
        if(\Auth::user()->role ==  'landlord') {
            $transactions = Transaction::where('user_id', $this->user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else if(\Auth::user()->role ==  'manager') {
            $manager=Manager::where('user_id',$this->user->id)->first();
            $transactions = Transaction::where('user_id', $manager->landlord_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $transactions = array();
        }
		return view('TenantSync::resident.transactions.index', compact('transactions'));	
    }
}  