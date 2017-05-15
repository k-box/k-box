
<header class=" header" role="header">

	<div class="top-header">

		<div class="u-pull-right">

			<a class="button button-primary" href="{{DmsRouting::download($document)}}" download="{{ $document->title }}">
				{{trans('panels.download_btn')}}
			</a>

		</div>

		<div class="logo">
			<a href="@if(isset( $is_user_logged ) && $is_user_logged){{$current_user_home_route}}@else{{route('frontpage')}}/@endif">
				&nbsp;
			</a>
		</div>
		<h4 class="title">
			
			{{$document->title}}
			
		</h4>

	</div>

</header>