@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Payment Amount</h4>
			</div>

			<form class="form form-horizontal" action="choosepaymentmethod" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="property" value="{{$paymentDetails['property']}}">
				@foreach ($paymentTypes as $paymentType)				
					<div class="form-group">
						<label class="control-label col-sm-3">{{$paymentType->payment_type}}</label>
						<div class="col-sm-9">
							<input class="form-control" type="numeric" name="{{$paymentType->payment_type}}" placeholder="0"/>
						</div>
					</div>
				@endforeach

				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection

