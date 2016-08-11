<?php namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DeviceUpdateMaintenance;
use TenantSync\Models\Device;
use TenantSync\Models\Transaction;
use TenantSync\Models\OverdueUsage;
use TenantSync\Models\OverdueType;
use TenantSync\Models\Property;
use TenantSync\Models\User;
use App\Http\Controllers\Auth;
use TenantSync\Mutators\PropertyMutator;use Response;


class DepositController extends Controller {
    
public function __construct()
    {
    	parent::__construct();
        $this->middleware('auth');
    }

	public function home()
    {
        $transactions = Transaction::where('payment_from_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->get();
		return view('TenantSync::resident.deposits.index', compact('transactions'));	
    }
}  