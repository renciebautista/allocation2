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
		{{ HTML::style('assets/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css') }}
		{{ HTML::style('assets/plugins/chosen_v1.4.1/bootstrap-chosen.css') }}
		{{ HTML::style('assets/plugins/DataTables-1.10.4/css/jquery.dataTables.min.css') }}
		{{ HTML::style('assets/plugins/FixedColumns-3.0.2/css/dataTables.fixedColumns.min.css') }}
		{{ HTML::style('assets/plugins/ColVis-1.1.1/css/dataTables.colVis.min.css') }}
		{{ HTML::style('assets/plugins/fancytree-2.6.0/skin-xp/ui.fancytree.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap-datetimepicker-3.1.3/css/bootstrap-datetimepicker.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap3-editable/css/bootstrap-editable.css') }}
		
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
				<div class="navbar-collapse collapse" id="navbar-main">
					<ul class="nav navbar-nav">
						@if(!Auth::user()->hasRole("ADMINISTRATOR"))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="transaction">Transaction <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="transaction">
								@if(Auth::user()->hasRole("PROPONENT"))
								<li>{{ HTML::linkRoute('activity.index', 'Activity') }}</li>   
								@endif
								@if(Auth::user()->hasRole("PMOG PLANNER"))
								<li>{{ HTML::linkRoute('downloadedactivity.index', 'Downloaded Activity') }}</li>  
								@endif
								@if(Auth::user()->inRoles(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']))
								<li>{{ HTML::linkAction('submittedactivity.index' , 'Submitted Activity') }}</li>  
								@endif  
							</ul>
						</li>
						@endif
						
						@if(Auth::user()->hasRole("ADMINISTRATOR"))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="maintenance">Maintenance <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="maintenance">
								<li>{{ HTML::linkRoute('group.index', 'Group') }}</li>  
								<li>{{ HTML::linkAction('UsersController@index' , 'User') }}</li>    

								<li>{{ HTML::linkRoute('cycle.index', 'Cycle') }}</li>  
								<li>{{ HTML::linkRoute('activitytype.index', 'Activity Type') }}</li>  
							</ul>
						</li>
						@endif

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Reports <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('ReportController@activities' , 'Activities') }}</li>  
							</ul>
						</li>

						<li>
							<a href="../help/">Help</a>
						</li>
					</ul>

					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">{{ ucwords(strtolower(Auth::user()->getFullname())) }} <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="download">  
								<li>{{ HTML::linkAction('ProfileController@index', 'Profile') }}</li>  
								<li>{{ HTML::linkAction('LoginController@logout' , 'Logout') }}</li>    
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="container">
			<div id="pageloading">
				<div class="row">
				  	<div class="col-md-6 col-md-offset-3">
				  		<h3 id="progress-animated">Please wait page loading....</h3>
				  		<div class="progress progress-striped active">
			                <div class="progress-bar" style="width: 100%"></div>
			            </div>
				  	</div>
				</div>
				
			</div>
			<div id="page">
				@yield('content')
			</div>
			
			<!-- <footer>
				<div class="row">
				  <div class="col-lg-12">

					<ul class="list-unstyled">
					  <li class="pull-right"><a href="#top">Back to top</a></li>
					  <li><a href="http://news.bootswatch.com" onclick="pageTracker._link(this.href); return false;">Blog</a></li>
					  <li><a href="http://feeds.feedburner.com/bootswatch">RSS</a></li>
					  <li><a href="https://twitter.com/bootswatch">Twitter</a></li>
					  <li><a href="https://github.com/thomaspark/bootswatch/">GitHub</a></li>
					  <li><a href="../help/#api">API</a></li>
					  <li><a href="../help/#support">Support</a></li>
					</ul>
					<p>Made by <a href="http://thomaspark.me" rel="nofollow">Thomas Park</a>. Contact him at <a href="mailto:thomas@bootswatch.com">thomas@bootswatch.com</a>.</p>
					<p>Code released under the <a href="https://github.com/thomaspark/bootswatch/blob/gh-pages/LICENSE">MIT License</a>.</p>
					<p>Based on <a href="http://getbootstrap.com" rel="nofollow">Bootstrap</a>. Icons from <a href="http://fortawesome.github.io/Font-Awesome/" rel="nofollow">Font Awesome</a>. Web fonts from <a href="http://www.google.com/webfonts" rel="nofollow">Google</a>.</p>

				  </div>
				</div>

			</footer> -->
		</div>
	{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}

	{{ HTML::script('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.js') }}

	{{ HTML::script('assets/plugins/twitter-bootstrap/js/bootstrap.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}

	{{ HTML::script('assets/plugins/chosen_v1.4.1/chosen.jquery.min.js') }}
	

	{{ HTML::script('assets/plugins/DataTables-1.10.4/js/jquery.dataTables.min.js') }}
	{{ HTML::script('assets/plugins/FixedColumns-3.0.2/js/dataTables.fixedColumns.min.js') }}
	{{ HTML::script('assets/plugins/ColVis-1.1.1/js/dataTables.colVis.min.js') }}

	{{ HTML::script('assets/js/selectchain.js') }}

	{{ HTML::script('assets/plugins/fancytree-2.6.0/jquery.fancytree.min.js') }}

	
	{{ HTML::script('assets/plugins/jquery.maskMoney/jquery.maskMoney.min.js') }}
	{{ HTML::script('assets/plugins/jquery-inputformat/jquery-inputformat.min.js') }}

	{{ HTML::script('assets/plugins/moment/moment.js') }}
	{{ HTML::script('assets/plugins/bootstrap-datetimepicker-3.1.3/js/bootstrap-datetimepicker.min.js') }}

	{{ HTML::script('assets/plugins/digitalBush/jquery.maskedinput_1.4.0/jquery.maskedinput.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap-table/bootstrap-table.min.js') }}

	{{ HTML::script('assets/plugins/ckeditor/ckeditor.js') }}
	{{ HTML::script('assets/plugins/ckeditor/adapters/jquery.js') }}

	{{ HTML::script('assets/plugins/ajax_table/js/ajax_table.js') }}

	{{ HTML::script('assets/plugins/jquery-validation-1.13.1/jquery.validate.min.js') }}
	{{ HTML::script('assets/plugins/jquery-validation-1.13.1/additional-methods.js') }}

	{{ HTML::script('assets/plugins/bootbox/bootbox.min.js') }}
	{{ HTML::script('assets/plugins/accounting_js/accounting.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js') }}


	{{ HTML::script('assets/js/function.js') }}

	<script type="text/javascript">
		

		$(document).ready(function() {
			
			$("#page").show();
			$("#pageloading").hide();
			@section('page-script')

			@show
		});
	</script>
	</body>
</html>
