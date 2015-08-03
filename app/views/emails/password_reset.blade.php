<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>For Approval</title>
	<meta http-equiv="Content-Type" content="text/html; charset=us-ascii"><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, START -->

	<style type="text/css">
	body{
		font-family: "Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif; 
		font-size: 13px;
	}

	</style><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, END -->
</head>
<body>
<p>{{ ucwords(strtolower($user['first_name'])) }},</p>

<p>We received a request to change your password on ETOP.</p>
<p>Click the link below to set a new password</p>
<a href='{{ URL::to('reset_password/'.$token) }}'>
    {{ URL::to('reset_password/'.$token)  }}
</a>

<p>If you don't want to change your password, you can ignore this email.</p>

<br>
<p>Thanks,</p>
<p>ETOP Administrator</p>
</body>


</html>