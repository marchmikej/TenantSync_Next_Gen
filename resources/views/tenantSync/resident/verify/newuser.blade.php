@extends('TenantSync::bare')

@section('content')
 			<form class="form form-horizontal" action="" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="device_id" value="{{$verifyInfo['device_id']}}">

				<div class="form-group">
					<label class="control-label col-sm-3">Verify Address</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="address" value="{{$verifyInfo['address']}}" disabled/>
					</div>
				</div>			
			
				<div class="form-group">
					<label class="control-label col-sm-3">Email</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="email" value="{{$verifyInfo['email']}}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">First Name</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="first_name" value="{{$verifyInfo['first_name']}}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">Middle Initial</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="middle_initial" value="{{$verifyInfo['middle_initial']}}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">Last Name</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="last_name" value="{{$verifyInfo['last_name']}}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">Password</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="password"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3">Confirm Password</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="password_confirmation"/>
					</div>
				</div>
				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
@endsection