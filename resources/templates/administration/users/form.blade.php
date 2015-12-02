
    @include('errors.list')

    @if(isset($edit_enabled) && !$edit_enabled)
    
        <div class="alert info">
			{!! trans('administration.accounts.edit_disabled_msg', ['profile_url' => route('profile.index')]) !!}
		</div>
    
    @endif


    <input type="hidden" name="_token" value="{{{ csrf_token() }}}"> 

    <p>
        <?php $email_field_name = isset($edit_enabled) ? 'change_email' : 'email'; ?>
        <label>{{trans('administration.accounts.labels.email')}}</label>
        @if( $errors->has('email') )
            <span class="field-error">{{ implode(",", $errors->get('email'))  }}</span>
        @endif
        @if( $errors->has('change_email') )
            <span class="field-error">{{ implode(",", $errors->get('change_email'))  }}</span>
        @endif
        <input type="text" name="email" value="{{old('email', isset($user) ? $user->email : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
    </p>

    <p>
        
        <label>{{trans('administration.accounts.labels.username')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input type="text" name="name" value="{{old('name', isset($user) ? $user->name : '')}}" />
    </p>
    
    @if(!isset($edit_enabled) || (isset($edit_enabled) && $edit_enabled))
    
    <p>
        <label>{{trans('administration.accounts.labels.perms')}}</label>
        @if( $errors->has('capabilities') )
            <span class="field-error">{{ implode(",", $errors->get('capabilities'))  }}</span>
        @endif
        @foreach($user_types as $type_key  => $type_value)
            <a href="#" class="user-grab" data-type="{{$type_key}}">{{trans('administration.accounts.types.' . $type_key)}}</a>
        @endforeach
        <ul class="checkbox-list">
        <?php $olds = old('capabilities', array()); ?>
        @foreach($capabilities as $capability)
            <li class="{{ implode(' ', $type_resolutor[$capability->key])}}"><input type="checkbox" class="{{ implode(' ', $type_resolutor[$capability->key])}}" id="cap-{{$capability->key}}" name="capabilities[]" value="{{$capability->key}}" 
                    @if(isset($user) && in_array($capability->key, $caps) || in_array($capability->key, $olds)) checked @endif >
                    <label for="cap-{{$capability->key}}"> {{trans('administration.accounts.capabilities.' . $capability->key)}}</label></li>
        @endforeach
        </ul>
    </p>
    
    @endif
    
    <p>
        
        <button type="submit">{{$submit_text}}</button> or <a href="{{route('administration.users.index')}}">Cancel</a>
    </p>
    
    
    <script>
        
        require(["jquery", 'lodash'], function(_$, __){

		  console.log('Something');
          
          var list = _$('.checkbox-list');
          
          console.log(list);
          
          _$('.user-grab').on('mouseover', function(evt){
             console.log(this);
             
             var tp = _$(this).data('type');
             
             list.find('.highlighted').removeClass('highlighted');
             list.find('.' + tp +'').addClass('highlighted');
             
             evt.preventDefault();
             return false; 
          });
          
          _$('.user-grab').on('click', function(evt){
             console.log(this);
             
             var tp = _$(this).data('type');
             
             list.find(':checkbox').attr('checked', false);
             list.find('.' + tp +':checkbox').click();
             
             evt.preventDefault();
             return false; 
          });
		
	   });
        
        
    </script>
        
        
        
        
        
