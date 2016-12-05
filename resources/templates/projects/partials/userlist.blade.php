<div class="selectable-users">
        
        @if(isset($description) && !is_null($description))
            <p class="description">{{ $description }}</p>
        @endif
        
        <?php $olds = old('users', array()); ?>

<div class="search-user">
    <input type="text" name="search-user" class="search-user__input js-search-user" placeholder="{{ trans('projects.labels.search_member_placeholder') }}">
</div>

        <div class="userlist">

        @forelse($users as $user)

            <div class="userlist__user" data-user="{{ $user->id }}">

                <div class="userlist__avatar">
                    @include('avatar.picture', ['image' => $user->avatar, 'inline' => false, 'user_name' => $user->name, 'no_link' => true])
                </div>
                
                <strong class="userlist__name">{{$user->name}}</strong>
                
                <span class="userlist__email"><a href="mailto:{{$user->email}}">{{$user->email}}</a></span>


                @if(!is_null($user->institution))
                    <span class="userlist__user--institution">
                        {{$user->institution->name}}
                    </span>
                @endif

                @if(isset($edit) && $edit)
                    <input type="checkbox" class="userlist__checkbox" checked name="users[]" value="{{$user->id}}" id="u-{{$user->id}}">
                @endif

                @if(isset($edit) && $edit)
                    <div class="userlist__actions">
                        <button class="userlist__remove-button hint--left js-user-remove" data-user="{{ $user->id }}" data-hint="{{ trans('projects.remove_user_hint') }}">{{ trans('actions.remove') }}</button>
                    </div>
                @endif

            </div>
        
        @empty
        
        	<span class="userlist__empty">{{ isset($empty_message) ? $empty_message : trans('projects.no_members')}}</span>
        	
        @endforelse

        </div>
    
    
</div>