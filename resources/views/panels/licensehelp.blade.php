<div class="c-panel__overflow">

<h2>{{$pagetitle}}</h2>

<span class="description">{{ trans('license::help.description_disclaimer') }}</span>


		<div class="widgets">

		@forelse($licenses as $license)

			<div class="widget c-widget license-box">

				
				<div>
					<div class="license-box__title">
						<h4>{{ $license->title }}</h4>
					
						
						{!! $license->icon or '' !!}
					</div>
					<div>
						
						{!! Markdown::convertToHtml($license->description) !!}
					</div>
						
					@if($license->license)
						<div><a href="{{ $license->license }}" target="_blank" rel="noopener noreferrer nofollow">{{ trans('administration.documentlicenses.view_license') }}</a></div>
					@endif
					
				</div>
			</div>

		@empty


        @endif
        </div>