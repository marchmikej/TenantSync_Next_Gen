@extends('TenantSync::resident/layout')

@section('head')

@endsection

@section('content')

@if(Auth::user()->company_id > 0)
	<h2>Company: {{Auth::user()->company()->name}}</h2>
@endif

@endsection