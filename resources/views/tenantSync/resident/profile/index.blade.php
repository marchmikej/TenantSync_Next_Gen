@extends('TenantSync::resident/layout')

@section('content')

<div id="profile" class="row card" v-cloak>

	<div class="col-sm-6">
		<div class="card-header">
			<h4>Change Email</h4>
		</div>
		<form action="/profile/email" method="POST" class="form form-horizontal">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="form-group">
				<label class="control-label col-sm-3" for="email">Email</label>
				<div class="col-sm-9">
					<input class="form-control" type="email" name="email" placeholder="Email" value="{{ $user->email }}"/>
				</div>
			</div>

			<button class="btn btn-primary col-sm-3 col-sm-offset-9">Save</button>
		</form>
	</div>

	<div class="col-sm-6">
		<div>
			<div class="card-header">
				<h4>Password Reset</h4>
			</div>
			<form class="form form-horizontal" action="/profile/password" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				
				<div class="form-group">
					<label class="control-label col-sm-3" for="current_password">Current Password</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="current_password" placeholder="Current Password" value="{{ old('current_password') }}"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="password">Password</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="password" placeholder="Password" value="{{ old('password') }}"/>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3" for="password_confirmation">Confirm</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="password_confirmation" placeholder="Confirm" value="{{ old('password_confirmation') }}"/>
					</div>
				</div>

				<button class="btn btn-primary col-sm-3 col-sm-offset-9">Submit</button>

			</form>
		</div>
	</div>
</div>

<div class="row card">
	<div class="col-sm-6">
		<div class="card-header">
			<h4>Name</h4>
		</div>
		<form action="/profile/name" method="POST" class="form form-horizontal">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">

			<div class="form-group">
				<label class="control-label col-sm-3" for="email">First Name</label>
				<div class="col-sm-9">
					<input class="form-control" type="text" name="first_name" value="{{ $user->first_name }}"/>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="email">Last Name</label>
				<div class="col-sm-9">
					<input class="form-control" type="text" name="last_name" value="{{ $user->last_name }}"/>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-3" for="email">Middle Initial</label>
				<div class="col-sm-2">
					<input class="form-control" type="text" name="middle_initial" value="{{ $user->middle_initial }}"/>
				</div>
			</div>
			<button class="btn btn-primary col-sm-3 col-sm-offset-9">Save</button>
		</form>
	</div>

</div>


@endsection