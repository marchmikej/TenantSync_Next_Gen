@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Select Property for Payment</h4>
			</div>

			<table class="table">
			  <thead class="thead-default">
			    <tr>
			      <th>Property</th>
			      <th>Unit</th>
			      <th></th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($residents as $resident)
    				<tr>
				      <td>{{$resident->device()->address()}}</td>
				      <td>{{$resident->device()->location}}</td>
				      <td><a href="choosepaymentamount/{{$resident->device()->id}}">Make Payment</a></td>
			    	</tr>
				@endforeach
			  </tbody>
			</table>
			
		</div>
	</div>
</div>
@endsection
