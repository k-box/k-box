@props([
    'name',
    'documents' => null,
    'collections' => null,
    'placeholder' => '',
    'separators' => [',', ' '],
    'minimumInputLength' => 2,
    'template_result' => '(result) => { return (result.loading) ? result.text : result.name; }',
    'template_selection' => '(selection) => { return selection.name || selection.text; }',
])

<div  {{ $attributes->merge(['class' => '']) }} 
    data-placeholder="{{ $placeholder }}" 
    x-data="{ selection: '' }"
    @select-clear.window="selection='';"
    x-init="function(){
        this.select2 = $(this.$refs.select).select2({
			placeholder: '{{ $placeholder }}',
			tokenSeparators: [',', ' '],
			minimumInputLength: {{ $minimumInputLength }},
			templateResult: {{ $template_result }},
            templateSelection: {{ $template_selection }},
			ajax: {
				url: DMS.Paths.fullUrl(DMS.Paths.SHARES_TARGET_FIND),
				method: 'POST',
				dataType: 'json',
				data: function (params) {

					var filter = {collections: @json(optional($collections)->pluck('id')), documents: @json(optional($documents)->pluck('id'))};
					
					var queryParameters = {
					  s: params.term,
					  documents: filter.documents,
					  collections: filter.collections,
					  e: (this.val) ? this.val() : [],
					  _token: DMS.csrf()
					}
				
					return queryParameters;
				},
				processResults: function (data) {
					return {
						results: data.data
					};
				}
			  }
		});
        this.select2.on('select2:select', (event) => {
            this.selection = event.target.value;
        });
        this.$watch('selection', (value) => {
            this.select2.val(value).trigger('change');
        });
        return () => {
            this.select2.select2('destroy');
        };   
    }">
    <select x-ref="select" class="form-input w-full" name="{{$name}}[]" id="{{$name}}" multiple="multiple" style="min-width:auto !important">
    
                        
    </select>
</div>