
@extends('profile._layout')


@section('profile_page')

	<h4 class="my-4">{{trans('profile.storage.title')}}</h4>

	<div class="mb-8">
		@component('components.progress', ['percentage' => $percentage ?? 0, 'height' => 'h-3'])
			@unless ($unlimited)
				<p class="text-lg">
					{{ trans('widgets.storage.used', ['used' => $used, 'total' => $total]) }}
				</p>
			@else 
				<p class="text-lg">
					{{ trans('quota.unlimited_label') }}
				</p>
				<p class="text-gray-700">
					{{ trans('widgets.storage.used_single', ['used' => $used]) }}
				</p>
			@endunless
		@endcomponent
	
		@unless ($unlimited)
			{{-- scale indicator --}}
			<div class="w-full -mt-1 relative p-1 h-5 mb-4">
				@foreach ($scale as $item)
					<span class="absolute text-sm border-r pr-1 border-gray-700 text-gray-700 {{ $loop->index % 2 === 0 ? 'hidden lg:inline-block' : '' }} {{ $item === intval($percentage) || $item === $threshold ? 'font-bold' : '' }} {{ $item === 100 ? 'right-0' : '-translate-100' }}" @unless($item === 100) style="left:{{ $item }}%" @endif>{{ $item }}%</span>
				@endforeach
			</div>
			<div class="mb-4 text-right">
				<a href="{{ route('documents.trash') }}">{{ trans('profile.storage.view_trash') }}</a>
			</div>
		@endunless
	
	</div>

	@unless ($unlimited)
		<h5 class="mt-4">{{trans('quota.threshold.section')}}</h5>
		<p class="description text-gray-700 mb-4">{{trans('quota.threshold.hint')}}</p>

		<div class="mb-8">
			<form action="{{ route('profile.storage.update') }}" method="post">

				@csrf
				@method('PUT')

				<input type="number" class="form-input" name="threshold" id="threshold" value="{{$threshold}}" min="5" max="100" step="5">

				@if( $errors->has('threshold') )
					<span class="field-error">{{ implode(",", $errors->get('threshold'))  }}</span>
				@endif
				<p class="description text-gray-700">{{ trans('quota.threshold.acceptable_value') }}</p>

				<p class="mt-2">
					<button type="submit" class="button">{{trans('quota.threshold.update_btn')}}</button>
				</p>
			
			</form>
		</div>
	@endunless

@endsection
