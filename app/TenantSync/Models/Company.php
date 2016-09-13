<?php

namespace TenantSync\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
    	'name',
    ];

    public function transactions()
	{
		return $this->hasMany('TenantSync\Models\Transaction');
	}

}