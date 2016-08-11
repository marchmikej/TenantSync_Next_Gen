<?php 

namespace TenantSync\Models;

use Illuminate\Database\Eloquent\Model;

class UserProperty extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_properties';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 
		'device_id',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	public function user()
	{
		return $this->belongsTo('TenantSync\Models\User');
	}

	public function device()
	{
		return Device::find($this->device_id);
	}

}