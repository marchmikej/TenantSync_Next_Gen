@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Review Rent Roll Changes</h4>
			</div>
			@if($updateDetails['property']!= NULL)
				<h5>
					Update for Property: {{$updateDetails['property']->address}}
				</h5>
			@else
				<h5>
					Creating Property: {{$updateDetails['address']}}
				</h5>			
			@endif
			<form class="form form-horizontal" action="/upload/changerentroll" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
				<table class="table">
				  <thead class="thead-default">
				    <tr>
				      <th>Perform</th>
				      <th>Unit</th>
				      <th>Action</th>
				      <th>Tenant</th>
				      <th>Rent</th>
				    </tr>
				  </thead>
				  <tbody>
				  	@foreach ($changes as $key => $value)
		    			<tr>
					      <td><input checked type="checkbox" name="{{$key}}" value="PERFORM"</td>
					      <td>{{$value['Unit']}}</td>
					      <td>{{$value['Action']}}</td>
					      <td>{{$value['Tenant']}}</td>
					      <td>{{$value['Rent']}}</td>
				    	</tr>
					@endforeach
				  </tbody>
				</table>

				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection