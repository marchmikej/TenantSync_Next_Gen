@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Units</h4>
			</div>

			<table class="table">
			  <thead class="thead-default">
			    <tr>
			      <th>Address</th>
			      <th>Location</th>
			      <th>Rent</th>
			      <th>Residents</th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($devices as $device)
    				<tr>
				      <td><a href="/resident/residents/{{$device->id}}">{{$device->address()}}</a></td>
				      <td>{{$device->location}}</td>
				      <td>{{money_format("$%i",$device->rent_amount)}}</td>
				      <td>{{$device->countResidents()}}</td>
			    	</tr>
				@endforeach
			  </tbody>
			</table>
			
		</div>
	</div>
</div>
@endsection