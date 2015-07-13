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
		{{ HTML::style('assets/plugins/DataTables-1.10.7/css/jquery.dataTables.min.css') }}
		{{ HTML::style('assets/plugins/FixedColumns-3.0.4/css/dataTables.fixedColumns.min.css') }}
		{{ HTML::style('assets/plugins/ColVis-1.1.1/css/dataTables.colVis.min.css') }}
		{{ HTML::style('assets/plugins/fancytree-2.6.0/skin-xp/ui.fancytree.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap-datetimepicker-3.1.3/css/bootstrap-datetimepicker.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap3-editable/css/bootstrap-editable.css') }}

		{{ HTML::style('assets/plugins/uploadify/uploadify.css') }}


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
					<img src="{{asset('assets/images/Unilever-Logo.png')}}" style="float:left; margin-right:10px;">
					{{ HTML::linkAction('DashboardController@index', 'E-TOP',null, array('class' => "navbar-brand")) }}
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="navbar-collapse collapse" id="navbar-main">
					<ul class="nav navbar-nav">
						@if(Auth::user()->inRoles(['PROPONENT','PMOG PLANNER','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="transaction">My Activities <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="transaction">
								@if(Auth::user()->inRoles(['PROPONENT']))
								<li>{{ HTML::linkRoute('activity.index', 'All') }}</li> 
								<li>{{ HTML::linkAction('activity.index' , 'Unreleased',array('st' => ['1','2','3','4','5','6','7','8'],'title' => '')) }}</li>  
								<li>{{ HTML::linkAction('activity.index' , 'Released',array('st' => ['9'],'title' => '')) }}</li>  
								<li>{{ HTML::linkRoute('activity.create', 'Add New Activity') }}</li> 
								@endif

								@if(Auth::user()->inRoles(['PMOG PLANNER','FIELD SALES']))
								<li>{{ HTML::linkRoute('activity.index', 'All') }}</li> 
								<li>{{ HTML::linkAction('activity.index' , 'Unreleased',array('st' => ['1','2','3','4','5','6','7','8'],'title' => '')) }}</li>  
								<li>{{ HTML::linkAction('activity.index' , 'Released',array('st' => ['9'],'title' => '')) }}</li>  
								@endif
								@if(Auth::user()->inRoles(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']))
								<li>{{ HTML::linkAction('submittedactivity.index' , 'For Approval',array('st' => ['5'],'title' => '')) }}</li>  
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
								<li>{{ HTML::linkRoute('holidays.index', 'Holidays') }}</li>  
							</ul>
						</li>
						@endif

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Reports <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								@if(Auth::user()->inRoles(['PROPONENT','PMOG PLANNER']))
								<li>{{ HTML::linkAction('ReportController@activities' , 'All Released Activities') }}</li>  
								<li><a href="#">My Activity Summary Report</a></li>
								<li><a href="#">My Allocation Detail Report</a></li>  
								<li><a href="#">All Released Activity Summary Report</a></li>
								<li><a href="#">All Released Allocation Detail Report</a></li> 
								@endif

								@if(Auth::user()->inRoles(['FIELD SALES']))
								<li>{{ HTML::linkAction('ReportController@activities' , 'All Released Activities') }}</li>  
								<li><a href="#">My Activity Summary Report</a></li>
								<li><a href="#">My Allocation Detail Report</a></li>  
								<li><a href="#">All Released Activity Summary Report</a></li>
								<li><a href="#">All Released Allocation Detail Report</a></li>
								@endif
							</ul>
						</li>

						<li class="dropdown">
							{{ HTML::linkAction('CycleController@calendar', 'TOP Calendar',null, array()) }}
							
						</li>

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Exports <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('DownloadsController@cycles' , 'Download Released Activities by Cycle') }}</li>  
							</ul>
						</li>

						<li>
							<a href="../help/">Help</a>
						</li>
					</ul>

					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">{{ ucwords(strtolower(Auth::user()->getFullname())) }} [ {{ Auth::user()->roles[0]->name }} ]<span class="caret"></span></a>
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
				  		<h3 id="progress-animated">Loading....</h3>
				  		<div class="progress progress-striped active">
			                <div class="progress-bar" style="width: 100%"></div>
			            </div>
				  	</div>
				</div>
			</div>

			<div id="page">
				@yield('content')
			</div>
		</div>
	{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}

	{{ HTML::script('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.js') }}

	{{ HTML::script('assets/plugins/twitter-bootstrap/js/bootstrap.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}

	{{ HTML::script('assets/plugins/chosen_v1.4.1/chosen.jquery.min.js') }}
	

	{{ HTML::script('assets/plugins/DataTables-1.10.7/js/jquery.dataTables.min.js') }}
	{{ HTML::script('assets/plugins/FixedColumns-3.0.4/js/dataTables.fixedColumns.min.js') }}
	{{ HTML::script('assets/plugins/ColVis-1.1.1/js/dataTables.colVis.min.js') }}

	{{ HTML::script('assets/js/selectchain.js') }}

	{{ HTML::script('assets/plugins/fancytree-2.6.0/jquery.fancytree.min.js') }}

	
	{{ HTML::script('assets/plugins/jquery.maskMoney/jquery.maskMoney.min.js') }}
	{{ HTML::script('assets/plugins/jquery-inputformat/jquery-inputformat.js') }}

	{{ HTML::script('assets/plugins/moment/moment.js') }}
	{{ HTML::script('assets/plugins/bootstrap-datetimepicker-3.1.3/js/bootstrap-datetimepicker.min.js') }}

	{{ HTML::script('assets/plugins/digitalBush/jquery.maskedinput_1.4.0/jquery.maskedinput.min.js') }}
	{{ HTML::script('assets/plugins/jquery.inputmask-3.1.63/inputmask/jquery.inputmask.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap-table/bootstrap-table.min.js') }}

	{{ HTML::script('assets/plugins/ckeditor/ckeditor.js') }}
	{{ HTML::script('assets/plugins/ckeditor/adapters/jquery.js') }}

	{{ HTML::script('assets/plugins/ajax_table/js/ajax_table.js') }}

	{{ HTML::script('assets/plugins/jquery-validation-1.13.1/jquery.validate.min.js') }}
	{{ HTML::script('assets/plugins/jquery-validation-1.13.1/additional-methods.js') }}

	{{ HTML::script('assets/plugins/bootbox/bootbox.min.js') }}
	{{ HTML::script('assets/plugins/accounting_js/accounting.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap3-editable/js/bootstrap-editable.min.js') }}

	{{ HTML::script('assets/plugins/jquery.AreYouSure-1.9.0/jquery.are-you-sure.js') }}
	
	{{ HTML::script('assets/plugins/uploadify/jquery.uploadify.min.js') }}

	{{ HTML::script('assets/plugins/autosize-3.0.6/autosize.min.js') }}


	
	{{ HTML::script('assets/js/function.js') }}



	<script type="text/javascript">
		$(document).ready(function() {
			$("#page").show();
			$("#pageloading").hide();

			@yield('scripts')
			
			@section('page-script')

			@show
		});
	</script>
	</body>
</html>
