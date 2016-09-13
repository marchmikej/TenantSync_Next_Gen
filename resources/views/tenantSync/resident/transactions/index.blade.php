@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
			<div class="card-header">
				<h4>Transactions</h4>
			</div>
      <div class="table-responsive">
			<table class="table table-striped">
			  <thead class="thead-default">
			    <tr>
			      <th class="col-sm-1">Initiated Date</th>
			      <th class="col-sm-2">Address</th>
			      <th class="col-sm-2">Resident</th>
			      <th class="col-sm-2">Payment For</th>
			      <th class="col-sm-1">Amount</th>
			      <th class="col-sm-1">Type</th>
			      <th class="col-sm-1">Status</th>
			      <th class="col-sm-1">Reference Number</th>
			      <th class="col-sm-1">Auto Payment</th>
			    </tr>
			  </thead>
			  	<tbody>
			  		@foreach ($devices as $device)
				  		@foreach ($device->transactions as $transaction)
					  		@foreach ($transaction->getTypesArrary() as $key => $value)
		    				<tr>
						      	<td class="col-sm-1">{{$transaction->date}}</td>
						      	<td class="col-sm-2">{{$transaction->address()}}</td>
						      	<td class="col-sm-2">{{$transaction->getUser()->last_name . ", " . $transaction->getUser()->first_name . " " . $transaction->getUser()->middle_initial}}</td>
						      	<td class="col-sm-2">{{$key}}</td>
						      	<td class="col-sm-1">{{money_format("$%i",$value)}}</td>
						      	<td class="col-sm-1">{{$transaction->payment_type}}
						      	<td class="col-sm-1">{{$transaction->status}}</td>
						      	<td class="col-sm-1">{{$transaction->reference_number}}</td>
						      	<td class="col-sm-1">
						      	@if ($transaction->auto_payment_id>0) 
							      	Yes
							    @else
								    No
							    @endif
								</td>
					    	</tr>
					    	@endforeach
					    @endforeach
					@endforeach
			  </tbody>
			</table>	
		</div>
</div>
</div>
@endsection

