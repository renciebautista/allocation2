<!-- Modal -->
@if(($settings->change_password) && ($change_password))
	<div class="modal fade" id="changepasswordmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Password Policy</h4>
	      </div>
	      <div class="modal-body">
	        Your password has expired.  You need to change your password..
	      </div>
	      <div class="modal-footer">
	        <button id="changepassword" type="button" class="btn btn-primary">Ok</button>
	      </div>
	    </div>
	  </div>
	</div>
	@endif