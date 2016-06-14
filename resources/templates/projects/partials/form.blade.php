
<div class="six columns">
    
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
    @if(isset($manager_id)) 
    <input type="hidden" name="manager" value="{{ $manager_id }}">
    @endif 
    
    <p>
        
        <label>{{trans('projects.labels.name')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input type="text" name="name" value="{{old('name', isset($project) ? $project->name : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
    </p>
    
    <p>
        
        <label>{{trans('projects.labels.description')}}</label>
        @if( $errors->has('description') )
            <span class="field-error">{{ implode(",", $errors->get('description'))  }}</span>
        @endif
        <textarea name="description">{{old('description', isset($project) ? $project->description : '')}}</textarea>
    </p>
    
    
    <p>
        
        <button type="submit">{{$submit_btn}}</button> {{trans('actions.or_alt')}} <a href="@if(isset($cancel_route)) {{$cancel_route}} @else{{route('projects.index')}}@endif">{{trans('projects.labels.cancel')}}</a>
    </p>


</div>

<div class="six columns">
	
    <div class="selectable-users">
    			
        <label>{{trans('projects.labels.users')}}</label>
        
        <p class="description">{{trans('projects.labels.users_hint')}}</p>
        
        @if( $errors->has('users') )
            <span class="field-error">{{ implode(",", $errors->get('users'))  }}</span>
        @endif
        
        <?php $olds = old('users', array()); ?>
        @forelse($available_users as $user)
        
        	<input type="checkbox" name="users[]" value="{{$user->id}}" id="u-{{$user->id}}"
        		@if(in_array($user->id, $olds) || (isset($project_users) && in_array($user->id, $project_users))) checked @endif>
        	<label for="u-{{$user->id}}">
        		<span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>
        		{{$user->name}}
        	</label>
        
        @empty
        
        	<p class="description">{{trans('groups.people.no_users')}}</p>
        	
        @endforelse
    
    
    </div>
	
</div>