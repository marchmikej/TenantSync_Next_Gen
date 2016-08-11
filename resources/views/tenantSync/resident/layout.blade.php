@extends('TenantSync::bare')
@include('TenantSync::globals')

@section('topmenu')

<li><a href="/resident/transactions">Transactions</a></li>
<li><a href="/resident/deposits">Deposits</a></li>
<li><a href="/resident/choosepaymentlocation">Make Payment</a></li>
@if(Auth::user()->role ==  'landlord' || Auth::user()->role ==  'manager')
	<li><a href="/resident/devices">Units</a></li>
	<li><a href="/resident/properties">Properties</a></li>
	<li><a href="/resident/residents">Residents</a></li>
@endif

			<li class="dropdown">
				<a 
					class="dropdown-toggle" 
					id="dropdownMenu1" 
					data-toggle="dropdown" 
					aria-haspopup="true" 
					style="background-color: transparent !important;"
				>
			  		<span class="icon icon-menu" style="font-size: 1.2em;"></span>
				</a>
				
			  	<ul class="dropdown-menu nav-dropdown" aria-labelledby="dropdownMenu1">
			    	<li><a href="/{{Auth::user()->role}}/profile">Profile</a></li>

			    	@if(Auth::user()->role ==  'landlord')
			    	<li><a href="/{{Auth::user()->role}}/managers">Managers</a></li>
			    	@endif

				    <li role="separator" class="divider"></li>
				    <li><a href="/logout">Logout</a></li>
				</ul>
			</li>
		<!-- </ul> -->

@endsection
