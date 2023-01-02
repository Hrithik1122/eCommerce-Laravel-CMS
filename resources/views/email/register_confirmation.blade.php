<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
		{{__('messages.Hello')}},{{$user->first_name}}  {{$user->last_name}}
		<div style="width:100%">
		 <a href="{{url('confirmregister').'/'.$user->id}}">
		 	{{__('messages.confirm_email_address')}}
		 </a>
		</div>
</body>
</html>