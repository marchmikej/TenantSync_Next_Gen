@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div class="card-header">
			@if($paymentResponse['Result'] == "Approved")
				<h4>Thank you for your payment!</h4>
			@else
				<h4>There was an error in processing</h4>
			@endif
		</div>
		
		<table class="table">
			<thead>
				@if($paymentResponse['Result'] != "Approved")
				    <tr>
						<td>Payment For</td>
						<td>{{$paymentResponse['Error']}}</td>
					</tr>
				@endif
				<tr>
					<td>Reference Number</td>
					<td>{{$paymentResponse['RefNum']}}</td>
				</tr>				    				    
			</thead>
		</table>
	</div>
</div>
@endsection