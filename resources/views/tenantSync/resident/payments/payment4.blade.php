@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Account Details</h4>
			</div>

			<form class="form form-horizontal" action="/resident/reviewpayment" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">	
				<input type="hidden" name="property" value="{{$paymentDetails['property']}}">
				<input type="hidden" name="amount" value="{{$paymentDetails['amount']}}">
				<input type="hidden" name="paymentFor" value="{{$paymentDetails['paymentFor']}}">	
				<input type="hidden" name="payment_type" value="{{$paymentDetails['payment_type']}}">	
				@if($paymentDetails['payment_type']=='credit')	
					<div class="form-group">
						<label class="control-label col-sm-3">Card Holder</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="account_holder" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Billing Address</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="address" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Billing City</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="city" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Zip Code</label>
						<div class="col-sm-4">
							<input class="form-control" type="numeric" name="zip_code" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Choose State</label>
						<div class="col-sm-1">
							<select class="form-control" name="state">
								<option value="AL" default>AL</option>
								<option value="AK">AL</option>
								<option value="AS">AS</option>
								<option value="AZ">AZ</option>
								<option value="AR">AL</option>
								<option value="CA">CA</option>
								<option value="CO">CO</option>
								<option value="CT">CT</option>
								<option value="DE">DE</option>
								<option value="DC">DC</option>
								<option value="FM">FM</option>
								<option value="FL">FL</option>
								<option value="GA">GA</option>
								<option value="GU">GU</option>
								<option value="HI">HI</option>
								<option value="ID">ID</option>
								<option value="IL">IL</option>
								<option value="IN">IN</option>
								<option value="IA">IA</option>
								<option value="KS">KS</option>
								<option value="KY">KY</option>
								<option value="LA">LA</option>
								<option value="ME">ME</option>
								<option value="MH">MH</option>
								<option value="MD">MD</option>
								<option value="MA">MA</option>
								<option value="MI">MI</option>
								<option value="MN">MN</option>
								<option value="MS">MS</option>
								<option value="MO">MO</option>
								<option value="MT">MT</option>
								<option value="NE">NE</option>
								<option value="NV">NV</option>
								<option value="NH">NH</option>
								<option value="NJ">NJ</option>
								<option value="NM">NM</option>
								<option value="NY">NY</option>
								<option value="NC">NC</option>
								<option value="ND">ND</option>
								<option value="MP">MP</option>
								<option value="OH">OH</option>
								<option value="OK">OK</option>
								<option value="OR">OR</option>
								<option value="PW">PW</option>
								<option value="PA">PA</option>
								<option value="PR">PR</option>
								<option value="RI">RI</option>
								<option value="SC">SC</option>
								<option value="SD">SD</option>
								<option value="TN">TN</option>
								<option value="TX">TX</option>
								<option value="UT">UT</option>
								<option value="VT">VT</option>
								<option value="VI">VI</option>
								<option value="VA">VA</option>
								<option value="WA">WA</option>
								<option value="WV">WV</option>
								<option value="WI">WI</option>
								<option value="WY">WY</option>
							</select>
						</div>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Card Number</label>
						<div class="col-sm-4">
							<input class="form-control" type="numeric" name="card_number" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Expiration Month</label>
						<div class="col-sm-1">
							<select class="form-control" name="month">
								<option value="01" default>01</option>
								<option value="02" default>02</option>
								<option value="03" default>03</option>
								<option value="04" default>04</option>
								<option value="05" default>05</option>
								<option value="06" default>06</option>
								<option value="07" default>07</option>
								<option value="08" default>08</option>
								<option value="09" default>09</option>
								<option value="10" default>10</option>
								<option value="11" default>11</option>
								<option value="12" default>12</option>
							</select>
						</div>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Expiration Year</label>
						<div class="col-sm-1">
							<select class="form-control" name="year">
								<option value="16" default>2016</option>
								<option value="17" default>2017</option>
								<option value="18" default>2018</option>
								<option value="19" default>2019</option>
								<option value="20" default>2020</option>
								<option value="21" default>2021</option>
							</select>
						</div>	
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">CVV2</label>
						<div class="col-sm-4">
							<input class="form-control" type="numeric" name="cvv2" placeholder=""/>
						</div>
					</div>
				@elseif($paymentDetails['payment_type']=='check')
					<div class="form-group">
						<label class="control-label col-sm-3">Account Holder</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="account_holder" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Bank Name</label>
						<div class="col-sm-4">
							<input class="form-control" type="text" name="bank_name" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Routing Number (9 Digits)</label>
						<div class="col-sm-4">
							<input class="form-control" type="numeric" name="routing_number" placeholder=""/>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">Account Number</label>
						<div class="col-sm-4">
							<input class="form-control" type="numeric" name="account_number" placeholder=""/>
						</div>
					</div>	
				@endif

				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection

