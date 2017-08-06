<form role="import" id="import-form" class="c-form import-form" method="post" rv-on-submit="doImport" action="{{ route('documents.import') }}">

	<input type="hidden" value="remote" name="from">

	<div rv-visible="panel.remote" class="tab-content visible" id="remote">

		<textarea class="c-form__input c-form__input--larger" name="remote_import" id="remote-import" cols="30" rows="10" placeholder="{{trans('import.form.placeholder_web')}}">@if(isset($import_terms)){{ $import_terms }}@endif</textarea>


		<div class="c-form__buttons">
		<button type="submit" class="button button--primary" rv-enabled="canImport">
	    	{{trans('import.form.submit_web')}}
	    </button>
		</div>

	    <p class="description">{{trans('import.form.help_web')}}</p>

	</div>

</form>