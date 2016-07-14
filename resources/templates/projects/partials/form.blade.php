
<input type="hidden" name="_token" value="{{{ csrf_token() }}}">

@if(isset($manager_id)) 
<input type="hidden" name="manager" value="{{ $manager_id }}">
@endif

<div class="fieldbox">

    <div class="fieldbox__heading">{{trans('projects.labels.project_details')}}</div>

        
        <label>{{trans('projects.labels.name')}}</label>
        @if( $errors->has('name') )
            <span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
        @endif
        <input type="text" name="name" value="{{old('name', isset($project) ? $project->name : '')}}" @if(isset($can_change_mail) && !$can_change_mail) disabled @endif />

        
        <label>{{trans('projects.labels.description')}}</label>
        @if( $errors->has('description') )
            <span class="field-error">{{ implode(",", $errors->get('description'))  }}</span>
        @endif
        <textarea name="description">{{old('description', isset($project) ? $project->description : '')}}</textarea>


        @if(isset($create))
        <div id="add-members">

            @include('projects.partials.useradd', ['use_label' => true, 'hide_button' => true])

        </div>
        @endif

        <div>
            <button type="submit">{{$submit_btn}}</button> {{trans('actions.or_alt')}} <a href="@if(isset($cancel_route)) {{$cancel_route}} @else{{route('projects.index')}}@endif">{{trans('projects.labels.cancel')}}</a>
        </div>
    
</div>

@if(!isset($create))
<div class="fieldbox" id="add-members">

    @include('projects.partials.useradd')

 </div>
@endif

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
            console.log(this);

            var user = $(this).data('user');

            var elem = $(".userlist__user[data-user="+ user +"]");

            elem.addClass('userlist__user--removing');

            elem.find('.userlist__checkbox').remove();

            DMS.MessageBox.wait('{{ trans('projects.removing_wait_title') }}', '{{ trans('projects.removing_wait_text') }}');

            form.submit();

            return false;
        })

        @endif

    });
</script>