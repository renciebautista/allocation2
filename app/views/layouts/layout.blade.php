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
		{{ HTML::style('assets/plugins/DataTables-1.10.9/css/jquery.dataTables.min.css') }}
		{{ HTML::style('assets/plugins/FixedColumns-3.0.4/css/dataTables.fixedColumns.min.css') }}
		{{ HTML::style('assets/plugins/ColVis-1.1.1/css/dataTables.colVis.min.css') }}
		{{ HTML::style('assets/plugins/fancytree-2.10.2/skin-xp/ui.fancytree.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap-datetimepicker-3.1.3/css/bootstrap-datetimepicker.min.css') }}
		{{ HTML::style('assets/plugins/bootstrap3-editable/css/bootstrap-editable.css') }}

		{{ HTML::style('assets/plugins/uploadify/uploadify.css') }}

		{{ HTML::style('assets/plugins/handsontable-0.16.0/handsontable.full.min.css') }}

		{{ HTML::style('assets/plugins/submenu/css/bootstrap-submenu.min.css') }}

		{{ HTML::style('assets/plugins/kartik/css/dependent-dropdown.min.css') }}

		{{ HTML::style('assets/plugins/offline/offline-theme-default.css') }}
		{{ HTML::style('assets/plugins/offline/offline-language-english.css') }}

		{{ HTML::style('assets/plugins/jQuery.filer-1.0.5/css/jquery.filer.css') }}
		{{ HTML::style('assets/plugins/jQuery.filer-1.0.5/css/themes/jquery.filer-dragdropbox-theme.css') }}

		{{ HTML::style('assets/css/timeline.css') }}
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
						@if(Auth::user()->inRoles(['PROPONENT','PMOG PLANNER','GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR', 'FIELD SALES']))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="transaction">My Activities <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="transaction">
								@if(Auth::user()->inRoles(['PROPONENT', 'FIELD SALES']))
								<li>{{ HTML::linkRoute('activity.index', 'All') }}</li> 
								<li>{{ HTML::linkAction('activity.index' , 'Unreleased',array('st' => ['1','2','3','4','5','6','7','8'],'title' => '')) }}</li>  
								<li>{{ HTML::linkAction('activity.index' , 'Released',array('st' => ['9'],'title' => '')) }}</li>  

								@if(Auth::user()->ability([], ['create_national', 'create_customized']))
								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">New Activity</a>
								    <ul class="dropdown-menu">
								    	@if(Auth::user()->ability([], ['create_national']))
								      	<li>{{ HTML::linkAction('ActivityController@create', 'National', [1]) }}</li> 
								      	@endif
										@if(Auth::user()->ability([], ['create_customized']))
										<li>{{ HTML::linkAction('ActivityController@create', 'Customized', [2]) }}</li>
										@endif
								    </ul>
								</li>
								@endif
						
								 
								@endif

								@if(Auth::user()->inRoles(['PMOG PLANNER']))
								<li>{{ HTML::linkRoute('activity.index', 'All') }}</li> 
								<li>{{ HTML::linkAction('activity.index' , 'Unreleased',array('st' => ['1','2','3','4','5','6','7','8'],'title' => '')) }}</li>  
								<li>{{ HTML::linkAction('activity.index' , 'Released',array('st' => ['9'],'title' => '')) }}</li>  
								@endif
								@if(Auth::user()->inRoles(['GCOM APPROVER']))
								<li>{{ HTML::linkAction('submittedactivity.index' , 'For Approval',array('st' => ['5'],'title' => '')) }}</li>  
								@endif  
								@if(Auth::user()->inRoles(['CD OPS APPROVER']))
								<li>{{ HTML::linkAction('submittedactivity.index' , 'For Approval',array('st' => ['6'],'title' => '')) }}</li>  
								@endif 
								@if(Auth::user()->inRoles(['CMD DIRECTOR']))
								<li>{{ HTML::linkAction('submittedactivity.index' , 'For Approval',array('st' => ['7'],'title' => '')) }}</li>  
								@endif 

								@if(Auth::user()->isChannelApprover())
								<li>{{ HTML::linkAction('activity.preapprove' , 'Customized For Approval') }}</li>  
								@endif
							</ul>
						</li>
						@endif
						
						
						@if(Auth::user()->hasRole("ADMINISTRATOR"))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="maintenance">Maintenance <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="maintenance">
								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">Users Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('group.index', 'Roles') }}</li>  
								      	<li>{{ HTML::linkAction('DepartmentsController@index' , 'Departments') }}</li>
										<li>{{ HTML::linkAction('UsersController@index' , 'Users') }}</li>
										<li>{{ HTML::linkAction('UsersController@forapproval' , 'Users For Approval') }}</li>
								    </ul>
								</li> 



								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">Activity Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('cycle.index', 'Cycle') }}</li>  
								      	<li>{{ HTML::linkRoute('activity.index', 'Activities') }}</li>
										<li>{{ HTML::linkRoute('activitytype.index', 'Activity Type') }}</li>  
										<li>{{ HTML::linkRoute('holidays.index', 'Holidays') }}</li>  
								    </ul>
								</li>  
								 
								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">SOB Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('sobfilter.index', 'SOB Filters') }}</li> 
								      	<li>{{ HTML::linkRoute('sobgroup.index', 'SOB Groups') }}</li> 
								      	<li>{{ HTML::linkRoute('sobholiday.index', 'Ship To Holidays') }}</li> 
								    </ul>
								</li>  

								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">SKUS Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('brand.index', 'Brand') }}</li> 
								      	<li>{{ HTML::linkRoute('topsku.index', 'Reference SKUs') }}</li> 
										<li>{{ HTML::linkRoute('pricelist.index', 'Price List') }}</li> 
										<li>{{ HTML::linkRoute('launchskus.index', 'Launch SKUs') }}</li> 
										<li>{{ HTML::linkRoute('motherchildsku.index', 'Mother Child SKUs') }}</li> 
								    </ul>
								</li>

								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">Customer Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('area.index', 'Area') }}</li>  
										<li>{{ HTML::linkRoute('customer.index', 'Customer') }}</li> 
										<li>{{ HTML::linkRoute('customerremap.index', 'Customer Inactive / Active Mapping') }}</li>  
										<li>{{ HTML::linkRoute('shipto.index', 'Ship To') }}</li>  
										<li>{{ HTML::linkRoute('account.index', 'Account') }}</li> 
										<li>{{ HTML::linkRoute('channel.index', 'Channel') }}</li> 
										<li>{{ HTML::linkRoute('subchannel.index', 'Sub Channel') }}</li> 
										<li>{{ HTML::linkRoute('customermaster.index', 'Customer & Sales Masterfile') }}</li> 
								    </ul>
								</li>

								<li class="dropdown-submenu">
								    <a tabindex="0" data-toggle="dropdown">Job Order Maintenance</a>
								    <ul class="dropdown-menu">
								      	<li>{{ HTML::linkRoute('tasks.index', 'Tasks') }}</li>  
										<li>{{ HTML::linkRoute('subtasks.index', 'Sub Tasks') }}</li>  
								    </ul>
								</li> 
							</ul>
						</li>
						@endif

						@if(Auth::user()->ability([], ['manage_department_jo', 'manage_my_jo']))
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="transaction">Job Orders <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="transaction">
								<li>{{ HTML::linkRoute('joborders.index', 'Department Job Orders') }}</li> 
								<li>{{ HTML::linkRoute('myjoborders.index', 'My Job Orders') }}</li>
								
							</ul>
						</li>
						@endif

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Reports <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								@if(Auth::user()->inRoles(['PROPONENT','PMOG PLANNER']))
								<li>{{ HTML::linkAction('ReportController@activities' , 'All Released Activities',array('st' => ['9'])) }}</li>  
								@endif

								@if(Auth::user()->inRoles(['FIELD SALES']))
								<li>{{ HTML::linkAction('ReportController@activities' , 'All Released Activities',array('st' => ['9'])) }}</li>  
								@endif

								@if(Auth::user()->inRoles(['ADMINISTRATOR']))
								<li>{{ HTML::linkAction('ReportController@activities' , 'All Activities') }}</li>
								@endif

								<li>{{ HTML::linkAction('AllocationReportController@index' , 'Allocation Report') }}</li>


								@if(Auth::user()->inRoles(['ADMINISTRATOR','PROPONENT','PMOG PLANNER','COM APPROVER','CD OPS APPROVER','CMD DIRECTOR','SOB ASSISTANT']))
								<li>{{ HTML::linkAction('SobController@index' , 'Sales Order Booking Report') }}</li>
								@endif
								


								@if(Auth::user()->inRoles(['ADMINISTRATOR']))
								<li><a href="#">Calendar of Activities</a></li>
								<li><a href="#">Activity Details Report</a></li>  
								<li><a href="#">Activity Timings Report</a></li>
								<li><a href="#">PIS Summary Report</a></li>
								@endif

								
							</ul>
						</li>

						@if(Auth::user()->inRoles(['ADMINISTRATOR','SOB ASSISTANT']))

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">SOB <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('SobController@download' , 'Export SOB File') }}</li>  
							</ul>
						</li>
						@endif
						

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Exports <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('DownloadsController@cycles' , 'Download Activities') }}</li>  
							</ul>
						</li>

						<li class="dropdown">
							{{ HTML::linkAction('CycleController@calendar', 'TOP Calendar',null, array()) }}
							
						</li>

						@if(Auth::user()->hasRole("ADMINISTRATOR"))

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="report">Settings <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('SettingsController@index' , 'Settings') }}</li>  
							</ul>
						</li>
						@endif

						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="help">Help <span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="report">
								<li>{{ HTML::linkAction('FaqController@index', 'Frequently Asked Questions',null, array()) }}</li>  
								<li>{{ HTML::linkAction('FaqController@index', 'Documentation',null, array()) }}</li>  
							</ul>
						</li>

						
					</ul>

					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">{{ ucwords(strtolower(Auth::user()->getFullname())) }} [ {{ strtoupper(Auth::user()->department->department) }} ]<span class="caret"></span></a>
							<ul class="dropdown-menu" aria-labelledby="download">  
								<li>{{ HTML::linkAction('ProfileController@index', 'My Profile') }}</li>  
								<li>{{ HTML::linkAction('ProfileController@changepassword', 'Change Password') }}</li>  
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
				@if($_ENV['MAIL_TEST'])
			<h1 class="center" style="color:#f00; font-size: 30px;">Test Enviroment</h1>
			@endif
				@yield('content')
			</div>
		</div>


	
		
	{{ HTML::script('assets/js/jquery-1.11.1.min.js') }}

	{{ HTML::script('assets/plugins/jquery-ui-1.11.2/jquery-ui.min.js') }}

	{{ HTML::script('assets/plugins/twitter-bootstrap/js/bootstrap.min.js') }}

	{{ HTML::script('assets/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}

	{{ HTML::script('assets/plugins/chosen_v1.4.1/chosen.jquery.min.js') }}
	

	{{ HTML::script('assets/plugins/DataTables-1.10.9/js/jquery.dataTables.min.js') }}
	{{ HTML::script('assets/plugins/FixedColumns-3.0.4/js/dataTables.fixedColumns.min.js') }}
	{{ HTML::script('assets/plugins/ColVis-1.1.1/js/dataTables.colVis.min.js') }}

	{{ HTML::script('assets/js/selectchain.js') }}

	{{ HTML::script('assets/plugins/fancytree-2.10.2/jquery.fancytree.min.js') }}

	
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

	{{ HTML::script('assets/plugins/barcoder/barcoder.js') }}

	{{ HTML::script('assets/plugins/handsontable-0.16.0/handsontable.full.min.js') }}

	{{ HTML::script('assets/plugins/isdirty/jquery.dirtyforms.min.js') }}

	{{ HTML::script('assets/plugins/submenu/js/bootstrap-submenu.min.js') }}

	{{ HTML::script('assets/plugins/mindup/mindmup-editabletable.js') }}

	{{ HTML::script('assets/plugins/offline/offline.min.js') }}

	{{ HTML::script('assets/plugins/kartik/js/dependent-dropdown.min.js') }}
	
	{{ HTML::script('assets/plugins/jQuery.filer-1.0.5/js/jquery.filer.min.js') }}

	{{ HTML::script('assets/plugins/tinymce/js/tinymce/tinymce.min.js') }}
	
	{{ HTML::script('assets/js/function.js') }}

	@yield('add-script')

	<script type="text/javascript">
		$(document).ready(function() {

			$('#changepasswordmodal').modal({
			    backdrop: 'static',   // This disable for click outside event
			    keyboard: true        // This for keyboard event
			});
			$('#changepassword').on('click', function(){
				$('#changepasswordmodal').modal('hide');
				window.location.href = '/changepassword';
			});

			moment.locale('en', {
			  	week: { dow: 1 } // Monday is the first day of the week
			});

			$('.dropdown-submenu > a').submenupicker();

			$("#page").show();
			$("#pageloading").hide();

			@yield('scripts')
			@section('page-script')
			@show

			@if (Auth::user())
		    $(function() {
		      setInterval(function checkSession() {
		        $.get('/check-session', function(data) {
		          // if session was expired
		          if (data.guest) {
		            // redirect to login page
		            // location.assign('/auth/login');

		            // or, may be better, just reload page
		            location.reload();
		          }
		        });
		      }, 60000); // every minute
		    });
		@endif


		});
	</script>
	</body>
</html>
