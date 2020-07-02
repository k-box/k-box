
{{ csrf_field() }}

<div class="fieldbox">

    <div class="fieldbox__heading">{{trans('projects.labels.project_details')}}</div>

        <div class=" mb-4">
        <label>{{trans('projects.labels.name')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input type="text" name="name" class="form-input block mt-1" value="{{old('name', isset($project) ? $project->name : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />
        </div>

        
        <div class=" mb-4">
        <label>{{trans('projects.labels.description')}}</label>
        @if( $errors->has('description') )
            <span class="field-error">{{ implode(",", $errors->get('description'))  }}</span>
        @endif
        <textarea name="description" class="form-textarea block mt-1">{{old('description', isset($project) ? $project->description : '')}}</textarea>
        </div>
        
        <div class="mb-4">
            <label>{{trans('projects.labels.avatar')}}</label>
        
            @if(isset($project) && $project->avatar)
                <div class="">
                    <img class="max-w-xs h-auto js-avatar-image" src="{{ route('projects.avatar.index', $project->id) }}" />
                </div>

                <div class="mt-1 mb-2">
                    <button class="button button--danger js-remove-avatar" data-id="{{ $project->id }}">{{ trans('projects.labels.avatar_remove_btn') }}</button>
                </div>
            @endif
            
            @if( $errors->has('avatar') )
                <span class="field-error">{{ implode(",", $errors->get('avatar'))  }}</span>
            @endif
            <input name="avatar" class="js-project-avatar-input form-input block mt-1" id="avatar" type="file">
            <span class="description">{{trans('projects.labels.avatar_description')}}</span>
        </div>
</div>        
        
<div id="add-members" class="fieldbox">
       
        @include('projects.partials.useradd', ['use_label' => true, 'hide_button' => true])

        <div class="c-form__buttons">
            <button type="submit" class="button button--primary">{{$submit_btn}}</button> {{trans('actions.or_alt')}} <a href="@if(isset($cancel_route)) {{$cancel_route}} @else{{route('documents.projects.index')}}@endif">{{trans('projects.labels.cancel')}}</a>
        </div>
    
</div>

@if(!isset($create))

 <div class="fieldbox js-member-container" id="members">

    <div class="fieldbox__heading">{{trans('projects.labels.users_in_project', ['count' => isset($project_users) ? $project_users->count() : 0 ])}}</div>

    @include('projects.partials.userlist', ['users' => isset($project_users) ? $project_users : [], 'empty_message' => trans('projects.no_members'), 'edit' => isset($create) ? false : true ])

</div>

@endif

<script>

    require(['jquery'], function($){

        $(".js-select-users").select2({
            placeholder: "{{trans('projects.labels.users_placeholder')}}",
            tokenSeparators: [',', ' ']
        });

        @if(!isset($create))

        var form = $(".js-project-form");

        $(".js-member-container").on('click', '.js-user-remove', function(evt){

            var user = $(this).data('user');

            var elem = $(".userlist__user[data-user="+ user +"]");

            elem.addClass('userlist__user--removing');

            elem.find('.userlist__checkbox').attr('checked', 'checked');

            DMS.MessageBox.wait('{{ trans('projects.removing_wait_title') }}', '{{ trans('projects.removing_wait_text') }}');

            form.submit();

            return false;
        })

        @endif


        $(".js-remove-avatar").on('click', function(evt){
            evt.preventDefault();
            evt.stopPropagation();

            var $this = $(this); 
            var id = $this.data('id');

            DMS.MessageBox.question('{{ trans('projects.labels.avatar_remove_btn') }}', '{{ trans('projects.labels.avatar_remove_confirmation') }}', '{{ trans('actions.dialogs.yes_btn') }}', '{{ trans('actions.dialogs.no_btn') }}', function(choice){

                if(choice){

                    DMS.Services.ProjectAvatar.remove(id, function(){

                        $this.hide();
                        DMS.MessageBox.close();
                        $('.js-avatar-image').hide();

                    }, function(obj, err, errText){

                        if(obj.responseJSON && obj.responseJSON.status === 'error'){
                            DMS.MessageBox.error('{{ trans('projects.labels.avatar_remove_btn') }}', obj.responseJSON.message);
                        }
                        else if(obj.responseJSON && obj.responseJSON.error){
                            DMS.MessageBox.error('{{ trans('projects.labels.avatar_remove_btn') }}', obj.responseJSON.error);
                        }
                        else {
                            DMS.MessageBox.error('{{ trans('projects.labels.avatar_remove_btn') }}', '{{ trans('projects.labels.avatar_remove_error_generic') }}');
                        }

                    });
                }
                else {
                    DMS.MessageBox.close();
                }
            });
        });

        @if(!isset($create))

        var h = new holmes({

            input: '.js-search-user',

            find: '.userlist .userlist__user',

            placeholder: "{{ trans('projects.labels.search_member_not_found') }}",

            mark: true,

            class: {

                visible: 'visible',

                hidden: 'hidden'

            }

        });

        if($(".userlist .userlist__user").length > 0){
            h.start();
        }

        @endif

    });
</script>