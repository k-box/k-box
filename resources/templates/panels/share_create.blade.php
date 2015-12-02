




@if(isset($panel_title))
<h4 class="title">{{$panel_title}}</h4>
@else
<h4 class="title">{{trans_choice('share.share_panel_title', $elements_count, ['num' => $elements_count])}}</h4>
@endif

<div class="error-container">
	
</div>


@include('share.create')
