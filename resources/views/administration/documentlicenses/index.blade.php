@extends('administration.layout')

@section('breadcrumbs')
        
    <a href="{{route('administration.index')}}"  class="breadcrumb__item">{{trans('administration.page_title')}}</a> <span class="breadcrumb__item--current">{{$pagetitle}}</span>

@stop

@section('action-menu')


@stop

@section('page')
        
    @include('errors.list')

    @unless($settings_are_explicitly_configured)

        <div class="c-message">
            {{ trans('administration.documentlicenses.default_configuration_notice')}}
		</div>

    @endunless


    <form  method="post" class="c-form" action="{{route('administration.licenses.available.update')}}">
    
        {{ csrf_field() }}

        {{ method_field('PUT') }}


        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.documentlicenses.available.title')}}</h4>
            <p class="c-section__description">{{trans('administration.documentlicenses.available.description')}}
                <br>
                <a href="{{ route('help.licenses') }}" data-action="showLicenseHelp" target="_blank" rel="noopener nofollow">{{ trans('documents.edit.license_choose_help_button') }}</a>
            </p>

            @if(empty($licenses))
                <span class="field-error">{{trans('administration.documentlicenses.available.no_licenses_error')}}</span>
            @endif

            <div class="c-form__field">

                <label>{{trans('administration.documentlicenses.available.label')}}</label>
                @if( isset($errors) && $errors->has('available_licenses') )
                    <span class="field-error">{{ implode(",", $errors->get('available_licenses'))  }}</span>
                @endif

                @unless(empty($licenses))
                    @foreach($licenses as $license)
                        <span class="c-form__checkbox">
                            <input type="checkbox" @if($selected_licenses->contains('id', $license->id)) checked  @endif name="available_licenses[]" id="available_licenses_{{ $license->id }}" value="{{ $license->id }}">
                            <label for="available_licenses_{{ $license->id }}">&nbsp;{{ $license->title }}</label>

                            @if($license->license)
                            <a href="{{ $license->license }}" target="_blank" rel="noopener">{{trans('administration.documentlicenses.view_license')}}</a>
                            @endif
                        </span>
                    @endforeach
                @endunless
                
            </div>
            
            <div class="c-form__buttons {{ empty($licenses) ? 'c-form--blocked' : '' }}">            
                <button class="button button-primary" type="submit">{{trans('administration.documentlicenses.available.save')}}</button>
            </div>
        </div>
        
    </form>

    <form  method="post" class="c-form" action="{{route('administration.licenses.default.update')}}">
    
        {{ csrf_field() }}

        {{ method_field('PUT') }}

        <div class="c-section">
            <h4 class="c-section__title">{{trans('administration.documentlicenses.default.title')}}</h4>
            <p class="c-section__description">{{trans('administration.documentlicenses.default.description')}}</p>
            
            @if(empty($selected_licenses))
                <span class="field-error">{{trans('administration.documentlicenses.default.no_licenses_error')}}</span>
            @endif

            <div class="c-form__field {{ empty($selected_licenses) ? 'c-form--blocked' : '' }}">

                <label>{{trans('administration.documentlicenses.default.label') }}</label>
                @if( isset($errors) && $errors->has('default_license') )
                    <span class="field-error">{{ implode(",", $errors->get('default_license'))  }}</span>
                @endif

                <select class="c-form__input c-form__input--larger" name="default_license" id="default_license">

                    <option disabled selected value="">{{ trans('administration.documentlicenses.default.select') }}</option>                    

                    @forelse($selected_licenses as $license)
                        <option value="{{ $license->id }}" @if(old('default_license', $default_license ? $default_license->id : '') === $license->id) selected  @endif>{{ $license->title }}</option>
                    @empty
                    
                    @endforelse
                </select>

            </div>
            
            <div class="c-form__field {{ empty($selected_licenses) ? 'c-form--blocked' : '' }}">

                <label>{{trans('administration.documentlicenses.default.label') }}</label>
                @if( isset($errors) && $errors->has('default_license') )
                    <span class="field-error">{{ implode(",", $errors->get('default_license'))  }}</span>
                @endif

                @if($documents_without_license > 0)
                    <span class="c-form__checkbox">
                        <input type="checkbox" name="apply_to" id="apply_to" value="previous">
                        <label for="apply_to">&nbsp;{{ trans_choice('administration.documentlicenses.default.apply_default_license_to_previous', $documents_without_license) }}</label>
                    </span>
                @else
                    <span class="c-form__checkbox">
                        <input type="checkbox" name="apply_to" id="apply_to" value="all">
                        <label for="apply_to">&nbsp;{{ trans('administration.documentlicenses.default.apply_default_license_all') }}</label>
                    </span>
                @endif

            </div>
            
            <div class="c-form__buttons {{ empty($selected_licenses) ? 'c-form--blocked' : '' }}">            
                <button class="button button-primary" type="submit">{{trans('administration.documentlicenses.default.save')}}</button>
                
            </div>

        </div>
        
    </form>


@stop

@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['DMS', 'modules/panels'], function(DMS, Panels){


        $('[data-action="showLicenseHelp"]').on('click', function(evt){

            evt.preventDefault();
            evt.stopPropagation();
            
            Panels.openAjax('help-licenses', this, DMS.Paths.LICENSE_HELP + "?filter=all", {}, {});
            
        });

	});
	</script>

@stop