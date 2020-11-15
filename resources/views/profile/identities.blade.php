
@extends('profile._layout')


@section('profile_page')

	<div class="h-5"></div>
	
	<h4>{{trans('profile.identities')}}</h4>
	<p class="text-gray-700">{{trans('identities.hint')}}</p>
	
	<div class="h-5"></div>

	<div class=" mb-4">

		@foreach ($availableProviders as $provider)
			
			<x-oneofftech-identity-link action="connect" :parameters="['b' => 'profile']" :provider="$provider" class="button" />

			@error($provider)
				<div class="field-error block mt-2" role="alert">
					{{ $message }}
				</div>
			@enderror
		@endforeach

	</div>

	@forelse ($identities as $identity)

		<div 
			x-data="AsyncForm({question: false, deleting: false, deleted:false})"
			
			x-show="!deleted"
			 class="relative py-3 mb-2 flex justify-between {{ $loop->index % 2 === 0 ? '' : 'bg-gray-100' }} {{ ! $loop->last ? 'border-b border-gray-400' : '' }}" >

			<div class="w-4/6">
				<p class="font-bold">
					{{ \Illuminate\Support\Str::ucfirst($identity->provider) }}
				</p>
	
				<p class="text-sm">
					{{ trans('identities.connected_at') }}
					
					@date($identity->created_at)
				</p>
	
				@if ($identity->registration)
					<p class="text-sm">
						{{ trans('identities.registration') }}
					</p>
				@endif
			</div>

			<div class="w-1/6">
				<x-oneofftech-identity-link action="connect" label="{{ __('Link again') }}" :parameters="['b' => 'profile']" provider="gitlab" class="button"/>
			</div>

			<div class="w-1/6">



				@can('delete', $identity)
				
					<form  action="{{ route('profile.identities.destroy', ['identity' => $identity->getKey()]) }}"
						@form-submitting.self="deleting=true;question=false;"
						@form-errored.self="deleting=false;"
						@form-submitted.self="deleting=false;deleted=true;"
						x-on:submit.prevent="submit"
						method="post">
						@method('DELETE')
						@csrf

						<template x-if="errors">
							<div class="c-message c-message--error" x-text="errors"></div>
						</template>

						<button type="button" x-show="!question"  @click="if(!deleting || deleted){ question = !question;errors=null; }" class="button button--danger">
							<span class="" x-show="!deleting">{{trans('identities.unlink')}}</span>
							<span class="x-cloak " x-show="deleting">{{trans('identities.unlinking')}}</span>
						</button>

						<div class="x-cloak absolute right-0 top-0 shadow-md border border-gray-200 bg-gray-50 p-4" x-show="question">

							<p class="font-bold text-lg mb-2">{{ trans('identities.delete.question', ['provider' => $identity->provider]) }}</p>

							@if ($identity->registration)
								<p class="font-bold">{{ trans('identities.delete.registration_message') }}</p>
							@endif

							<p>{{ trans('identities.delete.message', ['provider' => $identity->provider]) }}</p>

							@if ($identity->registration)
								<p>
									{{ trans('identities.delete.registration_with') }}: <code>{{ auth()->user()->email }}</code>.
								</p>
								<p>
									<a href="{{ route('profile.password.index') }}" target="_blank">{{ trans('identities.delete.registration_set_password') }}</a>.
								</p>
							@endif

							<p class="mt-2">

								<button type="button" @click="question = !question" class="button p-1 w-32">{{trans('actions.cancel')}}</button>
								<button type="submit" class="button button--danger p-1 w-32">{{trans('identities.delete.confirm', ['provider' => $identity->provider])}}</button>
							</p>
						</div>

					</form>

				@endcan
				
			</div>
		</div>

	@empty
			
		@if(empty($availableProviders))
			<div class="empty">
				<p class="empty__message">{{ __('identities.not_available') }}</p>
			</div>
		@elseif($identities->isEmpty())
			<div class="empty">
				<p class="empty__message">{{ __('identities.nothing_connected') }}</p>
			</div>
		@endif

	@endforelse

@stop
