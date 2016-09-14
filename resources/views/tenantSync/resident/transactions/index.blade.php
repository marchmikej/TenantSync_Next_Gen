@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

<form class="form form-horizontal" action="/resident/transactions" method="POST">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<span class="col-sm-12">
		<div class="col-sm-4">
			Address/Resident:
			@if($searchArray['search']!='NOSEARCH')
				<input type="text" name="search" placeholder="{{$searchArray['search']}}">
			@else
				<input type="text" name="search" placeholder="search..">
			@endif
		</div>
		<div class="col-sm-4">		
			Start Date:
			@if($searchArray['start_date']!='NODATE')
				<input type="date" name="start_date" value="{{$searchArray['start_date']}}">
			@else
				<input type="date" name="start_date">
			@endif
		</div>
		<div class="col-sm-4">		
			End Date:
			@if($searchArray['end_date']!='NODATE')
				<input type="date" name="end_date" value="{{$searchArray['end_date']}}">
			@else
				<input type="date" name="end_date">
			@endif
		</div>
	</span>
	<span class="col-sm-12">
		<div class="col-sm-2">		
			<button class="btn btn-primary">Submit</button>
		</div>
	</span>
</form>
</div>

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
				  		@foreach ($transactions as $transaction)
					  		@foreach ($transaction->getTypesArrary() as $key => $value)
					  			@if($searchArray['search']!='NOSEARCH')
						  			@if(stripos($transaction->address(),$searchArray['search']) || stripos($transaction->getUser()->fullName(),$searchArray['search']))
		    				<tr>
						      	<td class="col-sm-1">{{$transaction->date}}</td>
						      	<td class="col-sm-2">{{$transaction->address()}}</td>
						      	<td class="col-sm-2">{{$transaction->getUser()->fullName()}}</td>
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
					    			@endif
					    		@else
					    					    				<tr>
						      	<td class="col-sm-1">{{$transaction->date}}</td>
						      	<td class="col-sm-2">{{$transaction->address()}}</td>
						      	<td class="col-sm-2">{{$transaction->getUser()->fullName()}}</td>
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
					    		@endif
					    	@endforeach
					    @endforeach
			  </tbody>
			</table>	
		</div>
</div>
</div>
@endsection

