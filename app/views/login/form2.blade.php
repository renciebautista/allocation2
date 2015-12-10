
<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">

	<title>Fixed Top Navbar Example for Bootstrap</title>

	<!-- Bootstrap core CSS -->
	{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/bootstrap.css') }}


	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/bootstrap.css') }}

	<!-- Custom styles for this template -->
	{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/navbar-fixed-top.css') }}

	<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
	<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->


	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
  </head>

  <body>

	<!-- Fixed navbar -->
	<nav class="navbar navbar-default navbar-fixed-top">
	  <div class="container">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand" href="#">E-TOP</a>
		</div>
	  </div>
	</nav>

	<div class="container">
	  	<div id="box">
	  		<div id="content">
		  		<div class="row">
			        <div class="col-sm-6">
			        	<h1>Register</h1>
			        	<form class="bs-example bs-example-form" data-example-id="simple-input-groups"> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        	</form>
			        </div><!-- /.blog-main -->

			        <div class="col-sm-6">
			        	<h1>Login</h1>
			          	<form class="bs-example bs-example-form" data-example-id="simple-input-groups"> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        		<div class="input-group"> 
			        			<span class="input-group-addon" id="basic-addon1">@</span> 
			        			<input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon1"> 
			        		</div> 
			        		<br> 
			        	</form>
			        </div><!-- /.blog-sidebar -->
			    </div>
		   	</div>
	  	</div>

	</div> <!-- /container -->


	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
	<script src="../../dist/js/bootstrap.min.js"></script>
	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
