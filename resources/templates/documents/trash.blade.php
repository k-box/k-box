@extends('documents.document-layout')

@section('page-action')

<div class="action-group">

		<a href="#" class="button" rv-disabled="nothingIsSelected" rv-on-click="restore">
			<span class="btn-icon icon-action-white icon-action-white-ic_settings_backup_restore_white_24dp"></span>{{trans('actions.restore')}}
		</a>
		
		
		<a href="#" class="button" rv-on-click="emptytrash">
			<span class="btn-icon icon-action-white icon-action-white-ic_delete_white_24dp"></span>{{trans('actions.empty_trash')}}
		</a>
		

</div>

@stop


@section('document_area')


	@include('documents.partials.listing')

@stop

@section('document_script_initialization')

@overwrite