
@if(isset($with_who)) 
	 
	@if($with_who instanceof \KBox\User)
		<span class="icon-social-black icon-social-black-ic_person_black_24dp"></span> {{$with_who->name}}
	@else
		<span class="btn-icon icon-social-black icon-social-black-ic_group_black_24dp"></span> {{$with_who->name}}
	@endif
		
@endif