<form role="import" id="import-form" class="import-form" method="post" rv-on-submit="doImport" action="{{ route('import') }}">
	
	<div class="radio-tab">
		
		<div class="radio-tab-item">
			<input type="radio" value="remote"  rv-on-change="showRemotePanel" name="from" id="remote" checked="true"><label for="remote">{{trans('import.form.select_web')}}</label>
		</div>

		<!--<div class="radio-tab-item">
			<input type="radio" value="folder" rv-on-change="showFolderPanel" name="from" id="folder"><label for="folder">{{trans('import.form.select_folder')}}</label>
		</div>-->

	</div>

	<div rv-visible="panel.folder" class="tab-content" id="folder">
	
    	<input type="text" placeholder="{{trans('import.form.placeholder_folder')}}" value="@if(isset($import_terms)){{ $import_terms }}@endif" name="folder_import" @if(empty($import_terms)) @endif />

		<button type="submit" class="button-primary" rv-enabled="canImport">
	    	{{trans('import.form.submit_folder')}}
	    </button>

	</div>

	<div rv-visible="panel.remote" class="tab-content visible" id="remote">

		<textarea name="remote_import" id="remote-import" cols="30" rows="10" placeholder="{{trans('import.form.placeholder_web')}}">@if(isset($import_terms)){{ $import_terms }}@endif</textarea>

		<button type="submit" class="button-primary" rv-enabled="canImport">
	    	{{trans('import.form.submit_web')}}
	    </button>

	    <p class="help">{{trans('import.form.help_web')}}</p>

	</div>

</form>