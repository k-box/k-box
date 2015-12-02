<footer class="container footer" role="footer">
	
	<p>
		
	&copy; <?php 
  $fromYear = 2014; 
  $thisYear = (int)date('Y'); 
  echo $fromYear . (($fromYear != $thisYear) ? '-' . $thisYear : '');?>. K-Link
	<span class="version"><?php echo Config::get('dms.version'); ?> {{\App::environment()}}</span>.

	@if(!isset($not_show_links))

		<span class="links">
			
			<a href="{{ route('help') }}">{{trans('pages.help')}}</a>
			
			<a href="mailto:tickets@klink.uservoice.com" id="support_trigger">{{trans('pages.support')}}</a>

			<!--<a href="{{ route('terms') }}">{{trans('pages.terms')}}</a>

			<a href="{{ route('privacy') }}">{{trans('pages.privacy')}}</a>-->

			<a href="{{ route('contact') }}">{{trans('pages.contact')}}</a>

		</span>

	@endif


	</p>


	<!-- Made in Italy (in front of a carwash) -->
</footer>