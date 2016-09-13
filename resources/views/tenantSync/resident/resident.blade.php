@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Resident Emails</h4>
			</div>

      <div class="table-responsive">
			<table class="table table-striped">
			  <thead class="thead-default">
			    <tr>
			      <th>Location</th>
			      <th>Name</th>
			      <th>Email</th>
			    </tr>
			  </thead>
			  <tbody>
				  	@foreach ($devices as $device)
				  		@foreach ($device->residents() as $resident)
	    				<tr>
					      <td>{{$device->address()}}</td>
					      <td>{{$resident->user->last_name . ", " . $resident->user->first_name . " " . $resident->user->middle_initial}}<td>
					      <td>{{$resident->user->email}}</td>
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