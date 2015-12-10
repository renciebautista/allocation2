
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
		<link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		{{ HTML::style('assets/plugins/bootstrap-3.3.6/css/sticky-footer-navbar.css') }}

		<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
		<!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
		<script src="../../assets/js/ie-emulation-modes-warning.js"></script>

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

		<!-- Begin page content -->
		<div class="container">
			<div class="row">
		        <div class="col-sm-8 logo">
		        	<img src="assets/images/logo_2.png" width="500">
		        </div><!-- /.blog-main -->

        		<div class="col-sm-4">
        			<div id="login" class="bg-form"> 
							{{ Form::open(array('action' => 'LoginController@dologin','class' => 'form-horizontal')) }}
        					<div class="form-group"> 
        						<div class="col-sm-12"> 
									{{ Form::text('name','',array('class' => 'form-control' , 'placeholder' => 'Email')) }}
        						</div> 
        					</div> 
        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							{{ Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password')) }}
        						</div>
        					</div> 

        					 
        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<div class="checkbox"> 
        								<label> <input type="checkbox"> Remember me </label> 
        								<a class="pull-right" href="">Forgot password?</a>
        							</div> 
    							</div> 
    						</div> 

    						<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<button type="submit" class="btn btn-primary">Sign in</button>
        						</div>
        					</div>
						{{ Form::close() }}
    				</div>

    				<div id="register" class="bg-form"> 
    					<h2><strong>New to E-TOP?</strong> Sign up</h2>
        				<form class="form-horizontal"> 

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="First Name"> 
        						</div> 
        					</div> 

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Middle Initial"> 
        						</div> 
        					</div>

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Last Name"> 
        						</div> 
        					</div>

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Username"> 
        						</div> 
        					</div>

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Email"> 
        						</div> 
        					</div>
        					
        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Contact Number"> 
        						</div> 
        					</div>

        					<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<input type="email" class="form-control" id="inputEmail3" placeholder="Department"> 
        						</div> 
        					</div> 

    						<div class="form-group"> 
        						<div class="col-sm-12"> 
        							<button type="submit" class="btn btn-primary">Sign up for E-TOP</button>
        						</div>
        					</div>
    					</form> 
    				</div>
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
		<script src="../../dist/js/bootstrap.min.js"></script>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>
