@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Payment Amount</h4>
			</div>
			<h3>Monthly Rent: {{money_format("$%i",$device->rent_amount)}}</h3>
			@foreach($device->additionalCharges as $additionalCharge)
				<h3>{{$additionalCharge->payment_type}}: {{money_format("$%i",$additionalCharge->amount)}}</h3>
			@endforeach
			<form class="form form-horizontal" action="choosepaymentmethod" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="property" value="{{$device->id}}">
				<div class="form-group">
					<label class="control-label col-sm-3">Rent</label>
					<div class="col-sm-9">
						<input class="form-control" type="numeric" name="Rent" placeholder="0"/>
					</div>
				</div>				
				@foreach ($device->additionalCharges as $additionalCharge)				
					<div class="form-group">
						<label class="control-label col-sm-3">{{$additionalCharge->payment_type}}</label>
						<div class="col-sm-9">
							<input class="form-control" type="numeric" name="{{$additionalCharge->payment_type}}" placeholder="0"/>
						</div>
					</div>
				@endforeach

				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection

