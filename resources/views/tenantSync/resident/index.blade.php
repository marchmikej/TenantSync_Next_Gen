@extends('TenantSync::resident/layout')

@section('head')

@endsection

@section('content')
@if(Auth::user()->company_id > 0)
<accounting-stats inline-template>
	<div class="card row">
		<div id="stats" class="col-sm-12">
			<h4 class="card-header">30 Day Overview {{Auth::user()->company()->name}}</h4>
			<div class="col-sm-6 col-md-3 card-column">
				<p class="text-center">Payments Taken</p>
				<p class="stat text-success text-center">
					{{$overview['number_payments']}}
				</p>
			</div>
			<div class="col-sm-6 col-md-3 card-column">
				<p class="text-center">Amount Processed</p>
				<p class="stat text-primary text-center">
					{{money_format("$%i",$overview['payment_sum'])}}
				</p>
			</div>
			<div class="col-sm-6 col-md-3 card-column">
				<p class="text-center">Number of Units</p>
				<p class="stat text-danger text-center">
					{{$overview['number_of_units']}}
				</p>
			</div>
			<div class="col-sm-6 col-md-3 card-column">
				<p class="text-center">Auto Payments</p>
				<p class="stat text-warning text-center">
					{{$overview['auto_payments']}}
				</p>
			</div>
		</div>
	</div>
	
</accounting-stats>

@endif

@endsection