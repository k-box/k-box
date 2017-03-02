
<div class="c-widget widget--sessions">

	<h5 class="widget__title">
		{{trans('widgets.user_sessions.title')}}
	</h5>


	@forelse ($active_users as $session)

		<div class="widget--sessions__user">
			@include('avatar.picture', ['image' => null, 'inline' => true, 'user_name' => $session['user'], 'no_link' => true])
			
			<div>
				<div class="widget--sessions__username">
					{{$session['user']}}
				</div> 
				<div class="widget--sessions__time">
					{{$session['time']}} 
				</div>
			</div>
		</div>

	@empty

		<p>{{trans('widgets.user_sessions.empty')}}</p>

	@endforelse

</div>