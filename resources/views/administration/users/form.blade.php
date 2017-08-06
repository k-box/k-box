
    @include('errors.list')

    @if(isset($edit_enabled) && !$edit_enabled)
    
        <div class="c-message c-message--info">
			{!! trans('administration.accounts.edit_disabled_msg', ['profile_url' => route('profile.index')]) !!}
		</div>
    
    @endif


    {{ csrf_field() }}

    <div class="c-form__field">
        <label>{{trans('administration.accounts.labels.email')}}</label>
        @if( $errors->has('email') )
            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
        @endif
        @if( $errors->has('change_email') )
            <span class="field-error">{{ implode(",", $errors->get('change_email'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="email" value="{{old('email', isset($user) ? $user->email : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
    </div>

    <div class="c-form__field">
        
        <label>{{trans('administration.accounts.labels.username')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="name" value="{{old('name', isset($user) ? $user->name : '')}}" />
    </div>


    @if(isset($institutions))

    <div class="c-form__field">
        
        <label>{{trans('administration.accounts.labels.institution')}}</label>
        @if( $errors->has('institution') )
            <span class="field-error">{{ implode(",", $errors->get('institution'))  }}</span>
        @endif

        <?php $old_institution = old('institution', isset($user) ? $user->getInstitution() : null) ?>
        
        <select class="c-form__input" name="institution">
            <option style="color:#808080">{{trans('administration.accounts.labels.select_institution')}}</option>
            @foreach($institutions as $inst)
                <option value="{{$inst->id}}" @if(isset($user) && !is_null($old_institution) && $old_institution === $inst->id) selected @endif>{{$inst->name}}</option>
            @endforeach
        </select>
        
    </div>
    @endif
    
    @if(!isset($edit_enabled) || (isset($edit_enabled) && $edit_enabled))
    
    <div class="c-form__field">
        <label>{{trans('administration.accounts.labels.perms')}}</label>
        @if( $errors->has('capabilities') )
            <span class="field-error">{{ implode(",", $errors->get('capabilities'))  }}</span>
        @endif
        @foreach($user_types as $type_key  => $type_value)
            <a href="#" class="user-grab" data-type="{{$type_key}}">{{trans('administration.accounts.types.' . $type_key)}}</a>
        @endforeach
        <ul class="checkbox-list">
        <?php $olds = old('capabilities', []); ?>
        @foreach($capabilities as $capability)
            <li class="{{ implode(' ', $type_resolutor[$capability->key])}}"><input type="checkbox" class="{{ implode(' ', $type_resolutor[$capability->key])}}" id="cap-{{$capability->key}}" name="capabilities[]" value="{{$capability->key}}" 
                    @if(isset($user) && in_array($capability->key, $caps) || in_array($capability->key, $olds)) checked @endif >
                    <label for="cap-{{$capability->key}}"> {{trans('administration.accounts.capabilities.' . $capability->key)}}</label></li>
        @endforeach
        </ul>
    </div>
    
    @endif
    
    <div class="c-form__buttons">
        <button type="submit" class="button button--primary">{{$submit_text}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.users.index')}}">{{trans('actions.cancel')}}</a>
    </div>
    
    
    
    <script>
        
        require(["jquery", 'lodash'], function(_$, __){

          var list = _$('.checkbox-list');
          
          _$('.user-grab').on('mouseover', function(evt){
             
             var tp = _$(this).data('type');
             
             list.find('.highlighted').removeClass('highlighted');
             list.find('.' + tp +'').addClass('highlighted');
             
             evt.preventDefault();
             return false; 
          });
          
          _$('.user-grab').on('click', function(evt){
             
             var tp = _$(this).data('type');

             list.find(':checkbox').attr('checked', false);
             list.find('.' + tp +':checkbox').attr('checked', true);
             
             evt.preventDefault();
             return false; 
          });
		
	   });
        
        
    </script>
        
        
        
        
        
