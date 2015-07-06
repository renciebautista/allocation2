<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>E-TOP</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		{{ HTML::style('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.css') }}
		{{ HTML::style('assets/plugins/twitter-bootstrap/css/bootstrap.css') }}
		{{ HTML::style('assets/plugins/twitter-bootstrap/css/bootswatch.min.css') }}
		{{ HTML::style('assets/plugins/font-awesome-4.2.0/css/font-awesome.min.css') }}
		{{ HTML::style('assets/css/styles.css') }}
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="../bower_components/html5shiv/dist/html5shiv.js"></script>
		  <script src="../bower_components/respond/dest/respond.min.js"></script>
		<![endif]-->
		<style type="text/css">
		body {
			  padding-top: 50px;
			}

		</style>
	</head>
	<body>
		<div class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<a href="../" class="navbar-brand">E-TOP</a>
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				
			</div>
		</div>

		<div class="container">
			@yield('content')
		</div>
	{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}

	{{ HTML::script('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.js') }}

	{{ HTML::script('assets/plugins/twitter-bootstrap/js/bootstrap.min.js') }}
	<script type="text/javascript">
		
		$(document).ready(function() {
		@section('page-script')

		@show
		});
	</script>
	</body>
</html>
