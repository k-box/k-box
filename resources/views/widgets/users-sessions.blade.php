
<div class="c-widget widget--sessions">

	<h4 class="widget__title">
		{{trans('widgets.user_sessions.title')}}
	</h4>


	@forelse ($active_users as $session)

		<div class="widget--sessions__user">

			@component('avatar.full', ['image' => null, 'name' => $session['user']])

				<div>
					<div class="widget--sessions__username">
						{{$session['user']}}
					</div> 
					<div class="widget--sessions__time">
						{{$session['time']}} 
					</div>
				</div>

			@endcomponent
			
		</div>

	@empty

		<p>{{trans('widgets.user_sessions.empty')}}</p>

	@endforelse

</div>