
@extends('profile._layout')


@section('profile_page')

	<h4>{{trans('profile.export_section')}}</h4>
	<p class="description">{{trans('profile.data-export.hint')}}</p>
	
	<form method="post"  class="c-form mb-8" action="{{route('profile.data-export.store')}}">
		
		{{ csrf_field() }}

		
		
		<div class="c-form__field">

			@if( $errors->has('time') )
				<span class="field-error">{{ implode(",", $errors->get('time'))  }}</span>
			@endif
			
			<button type="submit" class="button">{{trans('profile.data-export.generate')}}</button>
		</div>

		

	</form>

	<table class="c-table ">
		<thead class="c-table__head">
			<tr>
				<th style="width:60%">{{trans('profile.data-export.table.export_name')}}</th>
				<th style="width:20%">{{trans('profile.data-export.table.requested_at')}}</th>
				<th style="width:20%">{{trans('profile.data-export.table.available_until')}}</th>
			</tr>
		</thead>
		<tbody>

	@forelse ($exports as $export)
		  <tr>
			<td>
				@if($export->isExpired())
					{{ trans('profile.data-export.expired') }}
				@elseif($export->isPending())
					{{ trans('profile.data-export.pending') }}
				@else 
					<a href="{{ route('profile.data-export.store', ['name' => $export->name]) }}">{{ trans('profile.data-export.download') }}</a>
				@endif
			</td>
			<td>{{ $export->getCreatedAt() }}</td>
			<td>{{ $export->getPurgeAt() }}</td>
		  </tr>
	@empty

		<tr>
			<td class="">{{trans('profile.data-export.no-exports')}}</td>
		</tr>
		
	@endforelse
            </tbody>
        </table>
@stop
