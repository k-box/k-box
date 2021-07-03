
@extends('profile._layout')


@section('profile_page')

	<div class="h-5"></div>

	<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
		<div class="p-2 bg-gray-100 shadow-md flex flex-col items-center justify-center">

			@materialicon('toggle', 'star') {{trans_choice('profile.starred_count_label', $stars_count, ['number' => $stars_count])}}

		</div>
		<div class="p-2 bg-gray-100 shadow-md flex flex-col items-center justify-center">

			@materialicon('action','description') {{trans_choice('profile.documents_count_label', $documents_count, ['number' => $documents_count])}}

		</div>
		<div class="p-2 bg-gray-100 shadow-md flex flex-col items-center justify-center whitespace-nowrap overflow-hidden">
			@materialicon('action','label') {{trans_choice('profile.collections_count_label', $collections_count, ['number' => $collections_count])}}
		</div>
		<div class="p-2 bg-gray-100 shadow-md flex flex-col items-center justify-center">

			@materialicon('social','people') {{trans_choice('profile.shared_count_label', $shares_count, ['number' => $shares_count])}}

		</div>
	</div>

	<div class="h-5"></div>

	<form method="post" class="" action="{{route('profile.update')}}">
		
		{{ csrf_field() }}
		{{ method_field('PUT') }}
		<input type="hidden" name="_change" value="info">

		<h4>{{trans('profile.info_section')}}</h4>

		<div class=" mb-4">
			<label>{{trans('profile.labels.nicename')}}</label>
			<span class="description">{{trans('profile.labels.nicename_hint')}}</span>
			@if( $errors->has('name') )
				<span class="field-error">{{ implode(",", $errors->get('name'))  }}</span>
			@endif
			<input type="text"  class="form-input block" name="name" @if(isset($user)) value="{{$user->name}}" @endif />
		</div>
		
		<div class=" mb-4">			        
			<label>{{trans('profile.labels.organization_name')}}</label>
			<span class="description">{{trans('profile.labels.organization_name_hint')}}</span>
			@if( $errors->has('organization_name') )
				<span class="field-error">{{ implode(",", $errors->get('organization_name'))  }}</span>
			@endif
			<input type="text"  class="form-input block" name="organization_name" value="{{ old('organization_name', $user->organization_name)}}" />
		</div>
		
		<div class=" mb-4">			        
			<label>{{trans('profile.labels.organization_website')}}</label>
			<span class="description">{{trans('profile.labels.organization_website_hint')}}</span>
			@if( $errors->has('organization_website') )
				<span class="field-error">{{ implode(",", $errors->get('organization_website'))  }}</span>
			@endif
			<input type="text" placeholder="http://" class="form-input block" name="organization_website" value="{{ old('organization_website', $user->organization_website)}}" />
		</div>
		
		
		<div class=" mb-4">
			
			<button type="submit" class="button">{{trans('profile.update_profile_btn')}}</button>
		</div>


	</form>

	<div>

		<h4>{{trans('profile.language_section')}}</h4>

		<div class=" mb-4">
			
			<label>{{trans('profile.labels.language')}}</label>
			@if( $errors->has('language') )
				<span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
			@endif

			<x-language-selector :current="$language" />

			
		</div>


	</div>


@stop
