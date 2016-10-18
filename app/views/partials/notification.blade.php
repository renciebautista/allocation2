@if ((Session::has('message')) || $errors->any())
	<div class="alert alert-dismissable {{ Session::get('class') }}">
		<button class="close" data-dismiss="alert" type="button">×</button>
		{{ Session::get('message') }}
        <ul>
        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
        </ul>
	</div>
@endif

@if (Session::get('error'))
    <div class="alert alert-error alert-danger">
        @if (is_array(Session::get('error')))
            {{ head(Session::get('error')) }}
        @endif
    </div>
@endif

