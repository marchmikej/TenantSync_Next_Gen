@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-12">
		<div class="card-header">
			<h4>Upload Rent Roll</h4>
		</div>

		<form class="form form-horizontal" action="/upload/rentroll" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<div class="form-group">
				<label class="control-label col-sm-3">Upload File</label>
				<div class="col-sm-4">
					<input class="form-control" type="file" name="file"/>
				</div>
			</div>
			<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>
		</form>
	</div>
</div>
@endsection