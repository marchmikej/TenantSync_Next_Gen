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
    <h2>Payment Received Thank You!</h2>

    <div>
		<table class="table">
		  	<thead class="thead-default">
			    <tr>
			    	<th>Reference Number</th>
			    	<th>Tenant/Owner</th>
			    	<th>Property</th>
			      	<th>Payment Description</th>
			      	<th>Transaction Fee</th>
			      	<th>Total Payment</th>
			    </tr>
			 </thead>
			 <tbody>
			  	@foreach ($transactions as $transaction)
			  		<tr>
			  			<td>{{$transaction->reference_number}}</td>
			  			<td>{{$transaction->getUser()->last_name . ", " . $transaction->getUser()->first_name}}</td>
			  			<td>{{$transaction->address()}}
			  			<td>
				  			@foreach ($transaction->getTypesArrary() as $key => $value)
					     		{{$key . ":" . money_format("$%i",$value) . ", "}}
				    		@endforeach
				    	</td>
				    	<td>{{money_format("$%i",$transaction->transaction_fee)}}</td>
				    	<td>{{money_format("$%i",$transaction->amount+$transaction->transaction_fee)}}</td>
			    	</tr>
				@endforeach
			</tbody>
		</table>
  
    </div>
  </body>
</html>