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
	<h1>Hi {{ ucwords(strtolower($user['first_name'])) }}!</h1>

<p>Your report with a name of {{ $name }} is ready for download.</p>
<p>Click the link below to download the report</p>
<a href='{{ URL::to('ar/'.$token) }}'>
    {{ URL::to('ar/'.$token)  }}
</a>

<p>This generated report link will be auto deleted within 60 days.</p>

<br>
<p>Thanks,</p>
<p>ETOP Administrator</p>
</body>


</html>