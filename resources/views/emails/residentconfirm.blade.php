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
    <h2>Please Confirm</h2>

    <div>
		You have been requested to sign up as an approved resident for {{$emailInfo['address']}}.

		<a href="{{url('/')}}/verify/resident/{{$emailInfo['device_id']}}/{{$emailInfo['email']}}">Press to verify</a>
  
    </div>
  </body>
</html>