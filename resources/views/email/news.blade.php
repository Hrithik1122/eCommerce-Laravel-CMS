<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
		{{__('messages.Hello')}},{{$user['email']}}
		<div style="width:100%">
		  <?=html_entity_decode($user['msg'])?>
		</div>
	
</body>
</html>