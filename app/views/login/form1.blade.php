
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

		<title>E-TOP</title>

		<!-- Bootstrap core CSS -->
		{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/bootstrap.css') }}

		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        {{ HTML::style('assets/plugins/bootstrap-3.3.6/css/ie10-viewport-bug-workaround.css') }}

		<!-- Custom styles for this template -->
		{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/sticky-footer-navbar.css') }}

		<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
		<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
        {{ HTML::script('assets/plugins/bootstrap-3.3.6/js/ie-emulation-modes-warning.js') }}
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
				    {{ HTML::linkAction('LoginController@index', 'E-TOP',null, array('class' => "navbar-brand")) }}
				</div>
				
			</div>
		</nav>

		<!-- Begin page content -->
		<div class="container">
			<div class="row">
		        <div class="col-sm-8 hidden-xs logo">
		        	<img src="/assets/images/logo_2.png" width="500">
		        </div><!-- /.blog-main -->

        		<div class="col-sm-4">
        			@yield('content')
      			</div>
			</div>
		</div>

		<footer class="footer">
	      <div class="container">
	        <p class="text-muted">© 2014- {{ date('Y') }}, All Rights Reserved.</p>
	      </div>
	    </footer>


		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
        {{ HTML::script('assets/plugins/bootstrap-3.3.6/js/bootstrap.min.js') }}
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        {{ HTML::script('assets/plugins/bootstrap-3.3.6/js/ie10-viewport-bug-workaround.js') }}

	</body>
</html>
