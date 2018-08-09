
<div class="fieldbox__heading">{{trans('projects.labels.add_users')}}</div>

@if( $errors->has('users') )
    <span class="field-error">{{ implode(",", $errors->get('users'))  }}</span>
@endif

@if(isset($available_users))

    {{trans('projects.labels.users_hint')}}

    <div>

    <select class="js-select-users" name="users[]" id="users" multiple="multiple">

        @foreach ($available_users as $user)
            <option value="{{$user->id}}">{{$user->name}}</option>
        @endforeach
                        
    </select>

    

    </div>

    @if(!isset($hide_button))
    <div>
        <button type="submit" class="button">{{ trans('projects.labels.add_users_button') }}</button>
    </div>
    @endif

@else 

    {{trans('projects.no_user_available')}}

@endif