@extends('layout.sidebar')


@section('page')

    <div id="documents-list">

        @yield('additional_actions')

        @if(isset($info_message) && !is_null($info_message))
            <div class="c-message c-message--info mt-4">
                @materialicon('action', 'info_outline', 'button__icon fill-current'){{$info_message}}
            </div>
        @endif

        <div id="document-area">
            
            
            
            @if(isset($hint) && $hint)
            
                <div class="alert info">
                    {{$hint}}
                </div>
            
            @endif

            @include('errors.list')

            
            @if(isset($context) && ($context!=='recent' && $context!=='uploads' && $context!=='trash'))
                @include('documents.facets')
            @endif

            

            <div class="list {{$list_style_current}}" >

                <div class="list__header">

                    @hasSection ('list_header')

                        @yield('list_header')

                    @else
                        
                        <div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>
                        <div class="list__column list__column--hideable">{{trans('documents.descriptor.added_by')}}</div>
                        
                        <div class="list__column">{{trans('documents.descriptor.last_modified')}}</div>
                        <div class="list__column list__column--hideable">{{trans('documents.descriptor.language')}}</div>
                    @endif
                    
                </div>

                @yield('document_area')

            </div>



            @if( isset($pagination) && !is_null($pagination) )
                <div class="pagination-container">

                    {!! $pagination->render() !!}

                </div>
            @endif

        </div>

    </div>

    @include('documents.partials.uploadinfo')

@stop

@section('sidebar')

    @include('documents.menu')
	
@endsection

@section('breadcrumbs')

	@if(isset($filter) && !is_null($filter) )
		
		@if($context ==='group' && isset($context_group_instance))
		
			@if(isset($context_group_shared) && $context_group_shared)

				<a href="{{route('documents.sharedwithme')}}" class="breadcrumb__item">{{trans('documents.menu.shared')}}</a>

			@elseif($context_group_instance->is_private)
				
				<span class="breadcrumb__item">{{trans('groups.collections.personal_title')}}</span>
			
			@else 
				
				<a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('groups.collections.private_title')}}</a>
			
			@endif

		@elseif($context!=='projectspage')
		
			<a href="{{route('documents.index')}}" class="breadcrumb__item">{{trans('documents.page_title')}}</a>
		
		@endif
		
		 

		@if(isset($parents) && $context ==='group')

			@foreach ($parents as $parent)

				<a href="{{route('documents.groups.show', $parent->id)}}" class="breadcrumb__item">{{$parent->name}}</a>
			
			@endforeach

		@endif


		<span class="breadcrumb__item--current">{{$filter}}</span>

	@else

		<span class="breadcrumb__item--current">{{trans('documents.page_title')}}</span>

	@endif



@stop

@section('panels')

    @include('panels.generic')

@stop