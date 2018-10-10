
<div class="filters" id="filters-area">

	<div class="elastic-list-container @if(isset($filters) && !is_null($filters)) visible @endif" rv-visible="isVisible">
		<div id="elasticlist" class="elasticlist">
	
		@foreach($columns as $facet => $value)
		
			<div class="el-column" style="width:{{$width}}%" data-facet="{{$facet}}">
				<div class="el-header">{{$value['label']}}</div>
				<div class="el-filter-container">
					@if(isset($value['items']))
					
						@foreach($value['items'] as $f)
							
							<a href="{{DmsRouting::filterSearch($facet_filters_url, $current_active_filters, $facet, $f->value, $f->selected )}}" class="el-filter @if(property_exists($f, 'is_project') && $f->is_project) project--mark @elseif(property_exists($f, 'is_project')) personal--mark @endif @if($f->selected) current @endif @if($f->collapsed) collapsed @endif @if( property_exists($f, 'locked') && $f->locked) locked hint--top @endif" @if( property_exists($f, 'locked') && $f->locked)data-hint="{{trans('actions.filters.collection_locked')}}"@endif title="@if(property_exists($f, 'parents')) {{$f->parents}} @elseif(property_exists($f, 'label')) {{$f->label}} @else {{$f->value}} @endif " data-facet="{{$facet}}" data-filter="{{$f->value}}">
								<span class="el-filter-count">{{$f->count}}</span>
								<span class="el-filter-name">@if(property_exists($f, 'label')){{$f->label}}@else{{$f->value}}@endif</span>
							</a>
							
						@endforeach
					
					@endif
				</div>
					
			</div>
		@endforeach
		
		
		</div>
	</div>

	@yield('additional_filter_panels')
	
	@if(auth()->check())
		<div class="filter__buttons">

			<div class="filter__others">
				@yield('additional_filter_buttons')
			</div>

			<div class="filter__loader">
			
				<button class="button" rv-on-click="openClose">
					@materialicon('content', 'filter_list', 'button__icon'){{trans('actions.filters.filter')}}
				</button>

				
				@yield('additional_filters')
				

				@if(isset($filters) && !empty($filters))
					<a href="{{$clear_filter_url}}" class="button">
						@materialicon('content', 'clear', 'button__icon'){{trans('actions.filters.clear_filters')}}
					</a>
				@endif
				
			</div>

			

		</div>
	@endif

</div>