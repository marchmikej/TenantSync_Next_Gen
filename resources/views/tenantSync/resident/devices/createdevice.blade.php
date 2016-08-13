@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

		<div class="col-sm-12">
		<div>
			<div class="card-header">
				<h4>Create Unit</h4>
			</div>

			<form class="form form-horizontal" action="/resident/newunit" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			
				<div class="form-group">
					<label class="control-label col-sm-3">Unit Location</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="location" placeholder="Address"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3">Property</label>
					<div class="col-sm-9">
							<select class="form-control" name="property_id">
								@foreach($properties as $property)
									<option value="{{$property->id}}">{{$property->fullAddress()}}</option>
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