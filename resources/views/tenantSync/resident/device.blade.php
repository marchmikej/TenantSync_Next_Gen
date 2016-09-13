@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Units</h4>
			</div>
      <div class="table-responsive">
			<table class="table table-striped">
			  <thead class="thead-default">
			    <tr>
			      <th>Address</th>
			      <th>Location</th>
			      <th>Rent</th>
			      <th>Resident</th>
			      <th>Resident Emails</th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($properties as $property)
				  	@foreach ($property->devices as $device)
	    				<tr>
					      <td><a href="/resident/residents/{{$device->id}}">{{$device->address()}}</a></td>
					      <td>{{$device->location}}</td>
					      <td>{{money_format("$%i",$device->rent_amount)}}</td>
					      <td>{{$device->resident_name}}</td>
					      <td>{{$device->countResidents()}}</td>
				    	</tr>
					@endforeach
				@endforeach
			  </tbody>
			</table>
			</div>
		</div>
	</div>
</div>
@endsection