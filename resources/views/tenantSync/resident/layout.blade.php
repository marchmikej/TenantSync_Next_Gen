@extends('TenantSync::bare')
@include('TenantSync::globals')

@section('topmenu')

<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Payments</span></a>
    <ul class="dropdown-menu">
@if(Auth::user()->company_id == 0)
        <li><a href="/payment/choosepaymentlocation">One Time Payment</a></li>
        <li><a href="/autopay/choosepaymentlocation">Create Auto Payment</a></li>
		<li><a href="/resident/deposits">Payments Made</a></li>
@endif
        <li><a href="/autopay/viewautopayment">Auto Payments</a></li>
@if(Auth::user()->company_id > 0)
        <li><a href="/resident/transactions">Payments Received</a></li>
@endif
    </ul>
</li>
@if(Auth::user()->company_id > 0)
	<li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Properties</span></a>
        <ul class="dropdown-menu">
            <li><a href="/resident/properties">View Properties</a></li>
            <li><a href="/resident/newproperty">Add Property</a></li>
        </ul>
    </li>
	<li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Units</span></a>
        <ul class="dropdown-menu">
            <li><a href="/resident/devices/0">View Units</a></li>
            <li><a href="/resident/newunit">Add Unit</a></li>
        </ul>
    </li>    
	<li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Approvals</span></a>
        <ul class="dropdown-menu">
            <li><a href="/resident/residents/0">View Approval Emails</a></li>
            <li><a href="/resident/newresident">Add Approval Email</a></li>
        </ul>
    </li>
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
			    	<li><a href="/profile">Profile</a></li>

			    	@if(Auth::user()->role ==  'landlord')
			    	<li><a href="/{{Auth::user()->role}}/managers">Managers</a></li>
			    	@endif
			    	@if(Auth::user()->company_id > 0)
			    	<li><a href="/upload/home">Update Rent Roll</a></li>
			    	@endif

				    <li role="separator" class="divider"></li>
				    <li><a href="/logout">Logout</a></li>
				</ul>
			</li>
		<!-- </ul> -->

@endsection
