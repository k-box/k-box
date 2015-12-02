<div id="map" class="row document-map" style="">
		
	<div class="nine columns" id="map-area">
	
		map area
		
	</div>
	
	<div class="three columns map-panel" id="map-info" rv-on-click="mapListClick">
		
		<div rv-template="map">

            {% _.forEach(elements, function(el) { %}

			<div class="map-item">

                <a href="#" class="" data-inst="{# el.document_descriptor.institutionID #}" data-doc="{# el.document_descriptor.localDocumentID #}">

                    {# el.document_descriptor.title #}

                </a>
			
				<div class="meta">
					<span>{# el.document_descriptor.language #}</span>
				
					<span>{# el.document_descriptor.institutionName #}</span>
            	
					<span>{# el.document_descriptor.documentType #}</span>		
				</div>
			
			</div>

            {% }); %}
        </div>
		
	</div>
	
	
</div>