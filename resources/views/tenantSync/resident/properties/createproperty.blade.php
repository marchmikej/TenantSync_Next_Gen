@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Create Property</h4>
			</div>

			<form class="form form-horizontal" action="/resident/newproperty" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
				<div class="form-group">
					<label class="control-label col-sm-3">Address</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="address" placeholder="Address"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">City</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="city" placeholder="City"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">State</label>
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
					<label class="control-label col-sm-3">Zip</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="zip" placeholder="Zip"/>
					</div>
				</div>
				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection