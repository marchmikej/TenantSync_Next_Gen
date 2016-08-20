@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div class="card-header">
			<h4>Review Payment Details</h4>
		</div>
		
		<form class="form form-horizontal" action="submitpayment" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="property" value="{{$paymentDetails['property']}}">
			<input type="hidden" name="amount" value="{{$paymentDetails['amount']}}">
			<input type="hidden" name="paymentFor" value="{{$paymentDetails['paymentFor']}}">
			<input type="hidden" name="payment_type" value="{{$paymentDetails['payment_type']}}">
			<input type="hidden" name="account_holder" value="{{$paymentDetails['account_holder']}}">
			@if($paymentDetails['payment_type']=="credit")
				<input type="hidden" name="address" value="{{$paymentDetails['address']}}">
				<input type="hidden" name="city" value="{{$paymentDetails['city']}}">
				<input type="hidden" name="zip_code" value="{{$paymentDetails['zip_code']}}">
				<input type="hidden" name="state" value="{{$paymentDetails['state']}}">
				<input type="hidden" name="card_number" value="{{$paymentDetails['card_number']}}">
				<input type="hidden" name="cvv2" value="{{$paymentDetails['cvv2']}}">
				<input type="hidden" name="month" value="{{$paymentDetails['month']}}">
				<input type="hidden" name="year" value="{{$paymentDetails['year']}}">
			@elseif($paymentDetails['payment_type']=="check")	
				<input type="hidden" name="account_number" value="{{$paymentDetails['account_number']}}">
				<input type="hidden" name="routing_number" value="{{$paymentDetails['routing_number']}}">
				<input type="hidden" name="bank_name" value="{{$paymentDetails['bank_name']}}">
			@endif
			<table class="table">
				<tbody>
				    <tr>
					    <td>Payment For</td>
					    <td>{{$propertyDetails['paymentDisplay']}}</td>
				    </tr>
				    <tr>
					    <td>Property Address</td>
					    <td>{{$propertyDetails['property_address']}}</td>
				    </tr>				    
				    <tr>
					    <td>Property Unit</td>
					    <td>{{$propertyDetails['unit']}}</td>
				    </tr>	
				  	<tr>
					    <td>Payment Amount</td>
					    <td>{{money_format("$%i",$paymentDetails['amount'])}}</td>
				   	</tr>
	    			<tr>
					    <td>Processing Fee</td>
					    @if($paymentDetails['payment_type']=="check")
						    <td>{{money_format("$%i",3.45)}}</td>
						@elseif($paymentDetails['payment_type']=="credit")
						    <td>{{money_format("$%i",$paymentDetails['amount']*.0345)}}</td>
						@endif
				    </tr>
				    <tr>
					    <td>Payment Total</td>
					    @if($paymentDetails['payment_type']=="check")
						    <td>{{money_format("$%i",$paymentDetails['amount']+3.45)}}</td>
						@elseif($paymentDetails['payment_type']=="credit")
						    <td>{{money_format("$%i",$paymentDetails['amount']*1.0345)}}</td>
						@endif
				    </tr>
					<tr>
					    <td>Payment Type</td>
					    <td>{{$paymentDetails['payment_type']}}</td>
				    </tr>
				    <tr>
					    <td>Account Holder</td>
					    <td>{{$paymentDetails['account_holder']}}</td>
				    </tr>
				    @if($paymentDetails['payment_type']=="credit")
					    <tr>
						    <td>Billing Address</td>
						    <td>{{$paymentDetails['address']}}</td>
					    </tr>
					    <tr>
						    <td>Billing City</td>
						    <td>{{$paymentDetails['city']}}</td>
					    </tr>
					    <tr>
						    <td>Billing State</td>
						    <td>{{$paymentDetails['state']}}</td>
					    </tr>
					    <tr>
						    <td>Card Number</td>
						    <td>{{$paymentDetails['card_number']}}</td>
					    </tr>
					    <tr>
						    <td>Expiration</td>
						    <td>{{$paymentDetails['month']}}/{{$paymentDetails['year']}}</td>
					    </tr>
					    <tr>
						    <td>CVV2</td>
						    <td>{{$paymentDetails['cvv2']}}</td>
					    </tr>
				    @elseif($paymentDetails['payment_type']=="check")
					    <tr>
						    <td>Account Number</td>
						    <td>{{$paymentDetails['account_number']}}</td>
					    </tr>
					    <tr>
						    <td>Routing Number</td>
						    <td>{{$paymentDetails['routing_number']}}</td>
					    </tr>
					    <tr>
						    <td>Bank Name</td>
						    <td>{{$paymentDetails['bank_name']}}</td>
					    </tr>
				    @endif
				</tbody>
			</table>
			<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>
		</form>
	</div>
</div>
@endsection