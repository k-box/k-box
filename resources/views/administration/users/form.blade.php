
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

    @if(!isset($can_change_mail) || (isset($can_change_mail) && !$can_change_mail))

    <div class="c-form__field">
        <label>{{trans('login.form.password_label')}}</label>
        @if( $errors->has('password') )
        <span class="field-error">{{ implode(",", $errors->get('password'))  }}</span>
        @endif

        <input class="c-form__input" type="password" name="password" id="password" value="{{old('password', '')}}" />
        
        @if(isset($disable_password_sending) && $disable_password_sending)
        <span class="description">{{ trans('administration.accounts.labels.no_password_sending') }}</span>
        @endif
        
        @php
            $sending_disabled = (isset($disable_password_sending) && $disable_password_sending);
            $check_generate_password = old('generate_password', !$sending_disabled && !$errors->has('generate_password'));
            $check_send_password = old('send_password', !$sending_disabled || $errors->has('send_password'));
        @endphp

        <div class="c-form__field ">
            @if( $errors->has('generate_password') )
            <span class="field-error">{{ implode(",", $errors->get('generate_password'))  }}</span>
            @endif
            <span class="c-form__checkbox {{ isset($disable_password_sending) && $disable_password_sending ? 'c-form__checkbox--disabled' : '' }}">
                <input type="checkbox" class="" value="1" name="generate_password" id="generate_password" @if(isset($disable_password_sending) && $disable_password_sending) disabled @endif @if($check_generate_password) checked @endif>&nbsp;<label for="generate_password">{{ trans('administration.accounts.labels.generate_password') }}</label>
            </span>
        </div>
        <div class="c-form__field">
            @if( $errors->has('send_password') )
            <span class="field-error">{{ implode(",", $errors->get('send_password'))  }}</span>
            @endif
            <span class="c-form__checkbox {{ isset($disable_password_sending) && $disable_password_sending ? 'c-form__checkbox--disabled' : '' }}">
                <input type="checkbox" class="" value="1" name="send_password" id="send_password" @if(isset($disable_password_sending) && $disable_password_sending) disabled @endif @if($check_send_password) checked @endif>&nbsp;<label for="send_password">{{ trans('administration.accounts.labels.send_password') }}</label>
            </span>
        </div>
    </div>

    @endif

    <div class="c-form__field">
        
        <label>{{trans('administration.accounts.labels.username')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input class="c-form__input" type="text" name="name" value="{{old('name', isset($user) ? $user->name : '')}}" />
    </div>
    
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
            @if(isset($type_resolutor[$capability->key]))
            <li class="{{ implode(' ', $type_resolutor[$capability->key])}}"><input type="checkbox" class="{{ implode(' ', $type_resolutor[$capability->key])}}" id="cap-{{$capability->key}}" name="capabilities[]" value="{{$capability->key}}" 
                    @if(isset($user) && in_array($capability->key, $caps) || in_array($capability->key, $olds)) checked @endif >
                    <label for="cap-{{$capability->key}}"> {{trans('administration.accounts.capabilities.' . $capability->key)}}</label></li>
            @endif
        @endforeach
        </ul>
    </div>
    
    @endif
    
    <div class="c-form__buttons">
        <button type="submit" class="button button--primary">{{$submit_text}}</button> {{trans('actions.or_alt')}} <a href="{{route('administration.users.index')}}">{{trans('actions.cancel')}}</a>
    </div>
    
    
    
    <script>
        
        require(["jquery", 'lodash'], function(_$){

          var list = _$('.checkbox-list');
          var timeout = null;

          _$('.user-grab').on('mouseover', function(evt){
             
             var tp = _$(this).data('type');

             if(timeout){
                clearTimeout(timeout);
            }
             
             list.find('.highlighted').removeClass('highlighted');
             list.find('.' + tp +'').addClass('highlighted');
             
            timeout = setTimeout(function (){
                list.find('.highlighted').removeClass('highlighted');

                if(timeout){
                    clearTimeout(timeout);
                }
            }, 2000);

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


          var generatePassword = _$("#generate_password");
          isGeneratePasswordDisabled = generatePassword.attr('disabled');
          _$("#password").change(function(evt){

              if(this.value){
                  generatePassword.attr('checked', false);
              }
              else if(!isGeneratePasswordDisabled) {

                  generatePassword.attr('checked', true);
              }

          });
		
	   });
        
        
    </script>
        
        
        
        
        
