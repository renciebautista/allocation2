<h4>Timeline</h4>
<hr>
<div class="dashboard-widget-content">
    <ul class="list-unstyled timeline widget">
      @foreach($timelines as $timeline)
      <li>
        <div class="block">
          <div class="block_content">
            <h2 class="title"></h2>
            <span class="creator">{{ $timeline->createdby->getFullname() }}</span>
            <span>{{ $timeline->header_text }}</span>
           	<span class="byline"> {{ Carbon::createFromTimeStamp(strtotime($timeline->created_at))->diffForHumans()}}</span>
     
            <p class="excerpt"><?php echo nl2br($timeline->comment) ?>  
              @if(!empty($timeline->comment))
              ... <a href="">Read&nbsp;More</a>
              @endif
            </p>
          </div>
        </div>
      </li>
      @endforeach
    </ul>
</div>