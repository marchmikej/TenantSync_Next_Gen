@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Auto Payments</h4>
			</div>

			<table class="table">
			  <thead class="thead-default">
			    <tr>
			      <th>Status</th>
			      <th>Start Date</th>
			      <th>Remaining Payments</th>
			      <th>Reference Number</th>
			      <th>Property</th>
			      <th>Schedule</th>
			      <th>Amount</th>
			      <th>Payment Type</th>
			    </tr>
			  </thead>
			  <tbody>
			  @foreach($devices as $device)
			  	@foreach ($device->autoPayments as $autoPayment)
    				<tr>
				      <td>Still Working On</td>
				      <td>{{$autoPayment->initial_date}}</td>
				      <td>{{$autoPayment->num_payments}}</td>
				      <td>{{$autoPayment->customer_number}}</td>
				      <td>{{$autoPayment->device()->address()}}</td>
				      <td>{{$autoPayment->schedule}}</td>
				      <td>{{money_format("$%i",$autoPayment->amount)}}</td>
				      <td>{{$autoPayment->payment_type}}</td>
			    	</tr>
				@endforeach
			   @endforeach
			  </tbody>
			</table>
			
		</div>
	</div>
</div>
@endsection