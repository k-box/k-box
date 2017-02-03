
<header class=" header" role="header">

	<div class="top-header">

		<div class="u-pull-right">

			<a class="button button-primary" href="{{DmsRouting::download($document)}}" download="{{ $document->title }}">
				{{trans('panels.download_btn')}}
			</a>

		</div>

		<div class="logo">
			<a href="@if($is_user_logged){{route('dashboard')}}@else{{route('frontpage')}}/@endif">
				&nbsp;
			</a>
		</div>
		<h4 class="title">
			
			{{$document->title}}
			
		</h4>

	</div>

</header>