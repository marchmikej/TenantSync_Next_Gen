@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Transactions</h4>
			</div>

			<table class="table">
			  <thead class="thead-default">
			    <tr>
			      <th>Initiated Date</th>
			      <th>Address</th>
			      <th>Resident</th>
			      <th>Payment For</th>
			      <th>Amount</th>
			      <th>Type</th>
			      <th>Status</th>
			      <th>Reference Number</th>
			      <th>Auto Payment</th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($transactions as $transaction)
			  		@foreach ($transaction->getTypesArrary() as $key => $value)
    				<tr>
				      	<td>{{$transaction->date}}</td>
				      	<td>{{$transaction->address()}}</td>
				      	<td>{{$transaction->getUser()->last_name . ", " . $transaction->getUser()->first_name . " " . $transaction->getUser()->middle_initial}}</td>
				      	<td>{{$key}}</td>
				      	<td>{{money_format("$%i",$value)}}</td>
				      	<td>{{$transaction->payment_type}}
				      	<td>{{$transaction->status}}</td>
				      	<td>{{$transaction->reference_number}}</td>
				      	@if ($transaction->auto_payment_id>0) 
					      	<td>Yes</td>
					    @else
						    <td>No</td>
					    @endif
			    	</tr>
			    	@endforeach
				@endforeach
			  </tbody>
			</table>
			
		</div>
	</div>
</div>
@endsection