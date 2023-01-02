<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
		{{__('messages.Hello')}},{{$user->first_name}}  {{$user->last_name}}
		<div style="width:100%">
		   {{__('messages.order_id')}}:-{{$user->order_id}}
		</div>
		<div style="width:100%">
			{{$user->order_msg}}
		</div>
</body>
</html>