@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Properties</h4>
			</div>

      <div class="table-responsive">
			<table class="table table-striped">
			  <thead class="thead-default">
			    <tr>
			      <th>Address</th>
			      <th>Units</th>
			      <th>Total Rent</th>
			    </tr>
			  </thead>
			  <tbody>
			  	@foreach ($properties as $property)
    				<tr>
				      <td><a href="/resident/devices/{{$property->id}}">{{$property->fullAddress()}}</a></td>
				      <td>{{$property->deviceTotal()}}</td>
				      <td>{{money_format("$%i",$property->getRentAmount())}}</td>
			    	</tr>
				@endforeach
			  </tbody>
			</table>
			</div>
		</div>
	</div>
</div>
@endsection