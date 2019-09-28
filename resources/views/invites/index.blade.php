
@extends('profile._layout')


@section('profile_page')

	<h4 class="mt-4">{{trans('invite.label')}}</h4>
	<p class="mb-4 text-gray-700">{{trans('invite.hint')}}</p>

    @can('create', Invite::class)       
        <div class="mb-4">
            <a href="{{ route('profile.invite.create') }}" class="button">@materialicon('social', 'person_add', 'inline-block'){{ __('invite.create.title') }}</a>
        </div>
    @endcan


	<div class="mb-8">
	
		<div class="p-3 mb-2 text-gray-700 flex" >
            <div class="w-1/5">{{ trans('administration.accounts.labels.email') }}</div>
            <div class="w-1/5 ml-4">{{ trans('invite.labels.invited_on') }}</div>
            <div class="w-1/5 ml-4">{{ trans('invite.labels.accepted_on') }}</div>
            <div class="w-1/5 ml-4 text-center">{{ trans('invite.labels.status') }}</div>
            <div class="w-1/5 ml-4">&nbsp;</div>
        </div>

        @forelse ($invites as $invite)

            <div class="p-3 mb-2 {{ $loop->index % 2 === 0 ? '' : 'bg-gray-100' }} border-b border-gray-400 flex" >
                <div class="w-1/5">
                    {{ $invite->email }}
                </div>

                <div class="w-1/5 ml-4">{{ $invite->created_at->toDateString() }}</div>
                <div class="w-1/5 ml-4">{{ optional($invite->accepted_at)->toDateString() }}</div>
				<div class="w-1/5 ml-4 text-center">
					@if ($invite->wasAccepted())
						<span class=" rounded-full text-center py-1 px-3 text-sm font-normal bg-green-200 text-green-800">{{ trans('invite.status.accepted') }}</span>
					@else
						<span class=" rounded-full text-center py-1 px-3 text-sm font-normal bg-yellow-200 text-yellow-800">{{ trans('invite.status.pending') }}</span>	
					@endif
				</div>

                <div class="w-1/5 text-right">
                    
					<form class="ml-2 inline-block" action="{{ route('profile.invite.destroy', ['invite' => $invite->uuid]) }}" method="POST">
						@csrf
						@method('DELETE')
						<button type="submit" class="button button--danger px-2 py-1">{{ trans('invite.labels.remove_invite') }}</button>
					</form>
                </div>
            </div>
            
        @empty

            <div class=" p-10 bg-gray-100 text-gray-800 flex flex-col  items-center">

                <p class="leading-normal">
                    {{ trans('invite.labels.empty') }}
                </p>
            </div>
            
        @endforelse
	
	</div>

@endsection
