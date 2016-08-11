@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div class="card-header">
			<h4>Select Payment Method</h4>
		</div>
		<h5>
			Payment for:    {{$paymentDetails['paymentDisplay']}}<br>
			Payment amount: {{money_format("$%i",$paymentDetails['amount'])}}
		</h5>
		
		<form class="form form-horizontal" action="/resident/accountinfo" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="property" value="{{$paymentDetails['property']}}">
			<input type="hidden" name="amount" value="{{$paymentDetails['amount']}}">
			<input type="hidden" name="paymentFor" value="{{$paymentDetails['paymentFor']}}">
			<table class="table">
				  <thead class="thead-default">
				    <tr>
				    	<th></th>
				      	<th></th>
				      	<th>Standard Processing</th>
				    </tr>
				  </thead>
				  <tbody>
	    				<tr>
	    					<td><input type="radio" name="payment_type" value="check"></td>
					      	<td>Bank Account</td>
					      	<td>{{money_format("$%i",3.45)}} Fee</td>
				    	</tr>
						<tr>
							<td><input type="radio" name="payment_type" value="credit"></td>
					      	<td>Credit Card/Debit</td>
					      	<td>{{money_format("$%i",$paymentDetails['amount']*.0345)}} Fee</td>
				    	</tr>
				  </tbody>
			</table>
			<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>
		</form>
	</div>
</div>
@endsection