<?php namespace TenantSync\Models;

use Carbon\Carbon;
use TenantSync\Billing\Billable;
use Illuminate\Database\Eloquent\Model;

class AutoPayment extends Model {

	use Billable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'auto_payments';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 
		'device_id', 
		'customer_number', 
		'schedule', 
		'initial_date',
		'num_payments', 
		'amount', 
		'transaction_fee', 
		'payment_type',
		'description',
		'status',
		'payments_processed',
	];

	public function device()
	{
		return Device::find($this->device_id);
	}

	public function transactions()
	{
		return $this->hasMany('TenantSync\Models\Transaction');
	}

	public function user()
	{
		return User::find($this->user_id);
	}
}