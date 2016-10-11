<html lang="en-US">
  <head>
    <meta charset="utf-8">
    <style>
		table, th, td {
    		border: 1px solid black;
	    	border-collapse: collapse;
		}
	</style>
  </head>
  <body>
    <h2>Payments Received</h2>

    <div>
		<table class="table">
		  	<thead class="thead-default">
			    <tr>
			    	<th>Reference Number</th>
			    	<th>Date</th>
			    	<th>Status</th>
			    	<th>Tenant/Owner</th>
			    	<th>Property</th>
			      	<th>Payment Description</th>
			      	<th>Payment Type</th>
			      	<th>Amount</th>
			    </tr>
			 </thead>
			 <tbody>
			  	@foreach ($transactions as $transaction)
			  		@foreach ($transaction->getTypesArrary() as $key => $value)
				  		<tr>
				  			<td>{{$transaction->reference_number}}</td>
				  			<td>{{$transaction->date}}</td>
				  			<td>{{$transaction->status}}</td>
				  			<td>{{$transaction->getUser()->last_name . ", " . $transaction->getUser()->first_name}}</td>
				  			<td>{{$transaction->address()}}</td>
				  			<td>{{$key}}</td>
				  			<td>{{$transaction->payment_type}}</td>
					    	<td>{{money_format("$%i",$value)}}</td>
				    	</tr>
			    	@endforeach
				@endforeach
			</tbody>
		</table>
  
    </div>
  </body>
</html>