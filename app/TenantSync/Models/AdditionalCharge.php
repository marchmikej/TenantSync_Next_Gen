<?php

namespace TenantSync\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalCharge extends Model
{
    protected $fillable = [
    	'payment_type',
    	'device_id',
    	'amount',
    ];

	public function device()
	{
		return $this->belongsTo('TenantSync\Models\Device');
	}

}