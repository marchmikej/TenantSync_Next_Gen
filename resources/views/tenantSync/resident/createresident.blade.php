@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Add Resident</h4>
			</div>

			<form class="form form-horizontal" action="/resident/newresident" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
				<div class="form-group">
					<label class="control-label col-sm-3">Resident Email</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="email" placeholder="Email"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">Unit</label>
					<div class="col-sm-9">
							<select class="form-control" name="device_id">
							  	@foreach ($properties as $property)
								  	@foreach ($property->devices as $device)
										<option value="{{$device->id}}">{{$device->address()}}</option>
									@endforeach
								@endforeach
							</select>
					</div>
				</div>
				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>
@endsection