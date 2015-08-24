<div class="btn-group">
      <a href="#" class="btn btn-info">Options</a>
      <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">HTML Preview</a></li>
        <li><a href="{{ URL::action('ReportController@document', $activity->id ) }}">Download Document</a></li>
      </ul>
</div>