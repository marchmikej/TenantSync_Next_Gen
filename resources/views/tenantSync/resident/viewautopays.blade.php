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
			      <th>Start Date</th>
			      <th>Remaining Payments</th>
			      <th>Reference Number</th>
			      <th>Property</th>
			      <th>Schedule</th>
			      <th>Amount</th>
			      <th>Transaction Fee</th>
			      <th>Payment Type</th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($autoPayments as $autoPayment)
    				<tr>
				      <td>{{$autoPayment->initial_date}}</td>
				      <td>
				      @if($autoPayment->num_payments!=-1)
				      	{{$autoPayment->num_payments}}</td>
				      @else
				        Indefinite
				      @endif
				  	  </td>
				      <td>{{$autoPayment->customer_number}}</td>
				      <td>{{$autoPayment->device()->address()}}</td>
				      <td>{{$autoPayment->schedule}}</td>
				      <td>{{money_format("$%i",$autoPayment->amount)}}</td>
				      <td>{{money_format("$%i",$autoPayment->transaction_fee)}}</td>
				      <td>{{$autoPayment->payment_type}}</td>
			    	</tr>
				@endforeach
			  </tbody>
			</table>
			
		</div>
	</div>
</div>
@endsection