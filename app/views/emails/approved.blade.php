<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
  <title>ETOP Signup</title>
  <meta http-equiv="Content-Type" content="text/html; charset=us-ascii"><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, START -->

  <style type="text/css">
  body{
    font-family: "Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif; 
    font-size: 13px;
  }

  </style><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, END -->
</head>
<body>
  <h1>Hi {{ ucwords(strtolower($first_name)) }}!</h1>

<p>Thanks for creating an account with ETOP</p>
<p>This will be your account username and password, kindly change your password for security purpose after you have successfully logged in.</p>
<p>Username : {{ $username }}</p>
<p>Password : {{ $password }}</p>

<br>
<p>Thanks,</p>
<p>ETOP Administrator</p>
</body>


</html>