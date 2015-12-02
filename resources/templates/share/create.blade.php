

<form method="POST" action="{{route('shares.store')}}" class="share-form">


	<div class="row">

		<p>{{trans('share.with_label')}}</p>
		
		<div class="scrollable scrollable-medium">

		@foreach($people as $person)
			
			<div class="user-grab">				
				<input type="checkbox" name="with_people[]" value="{{$person->id}}" id="people-{{$person->id}}"><label for="people-{{$person->id}}"><span class="btn-icon icon-social-black icon-social-black-ic_group_black_24dp"></span>{{$person->name}}</label>
			</div>

		@endforeach
		<div></div>
		@foreach($users as $user)
			<div class="user-grab">				
				<input type="checkbox" name="with_users[]" value="{{$user->id}}" id="user-{{$user->id}}"><label for="user-{{$user->id}}"><span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>{{$user->name}}</label>
			</div>
		@endforeach

		</div>

	</div>

	<div style="display:none">

		<p>{{trans('share.what_label')}}</p>


		@if($has_documents)

			@foreach($documents as $document)

				<input type="checkbox" checked name="documents[]" value="{{$document->id}}" id="document-{{$document->id}}"><label for="document-{{$document->id}}">{{$document->title}}</label>

			@endforeach

		@endif


		@if($has_groups)

			@foreach($groups as $group)

				<input type="checkbox" checked name="groups[]" value="{{$group->id}}" id="group-{{$group->id}}"><label for="group-{{$group->id}}">{{$group->name}}</label>

			@endforeach


		@endif

	</div>

	<button type="submit" class="button">{{trans('share.share_btn')}}</button> {!!trans('actions.or', ['action' => '<a href="#"  class="cancel">'.trans('actions.cancel').'</a>'])!!} 


</form>
