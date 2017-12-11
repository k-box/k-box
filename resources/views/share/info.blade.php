
@if(isset($share) && !empty($share->sharedwith))

		@if(is_array($share->sharedwith))

			@forelse($share->sharedwith as $with)
				<div class="user-grab">
					@if($with instanceof \KBox\User)
						<span class="icon-social-black icon-social-black-ic_person_black_24dp"></span> {{$with->name}}
					@else
						<span class="btn-icon icon-social-black icon-social-black-ic_group_black_24dp"></span>{{$with->name}}
					@endif
				</div>
			@empty 

				<p>{{trans('panels.not_shared')}}</p>

			@endforelse

		@else 
		 
			@if($share->sharedwith instanceof \KBox\User)
				<span class="icon-social-black icon-social-black-ic_person_black_24dp"></span> {{$share->sharedwith->name}}
			@else
				<span class="btn-icon icon-social-black icon-social-black-ic_group_black_24dp"></span> {{$share->sharedwith->name}}
			@endif

		@endif

@else 

<p>{{trans('panels.not_shared')}}</p>

@endif