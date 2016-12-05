<?php namespace KlinkDMS\Http\Controllers;

use Carbon\Carbon;

use GuzzleHttp\Client as HttpClient;

class SupportPagesController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
   * [$adapter description]
   * @var \Klink\DmsAdapter\KlinkAdapter
   */
  private $adapter = NULL;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService)
	{
		$this->adapter = $adapterService;
	}



	public function terms()
	{
		$fallback = base_path('resources/assets/pages/en/terms-of-use.md');
		$path = base_path('resources/assets/pages/'. app()->getLocale() .'/terms-of-use.md');
		
		$file_content = file_get_contents( @is_file($path) ? $path : $fallback );

		$page_text = \Markdown::convertToHtml($file_content);

		return view('static.page', ['pagetitle' => trans('pages.service_policy'), 'page_content' => $page_text]);
	}


	public function privacy()
	{

		$help_file_content = file_get_contents( base_path('resources/assets/pages/privacy.md') );

		$page_text = \Markdown::convertToHtml($help_file_content);

		return view('static.page', ['page_title' => trans('pages.privacy'), 'page_content' => $page_text]);
	}

	public function help()
	{
		
		$fallback = base_path('resources/assets/pages/en/help.md');
		$path = base_path('resources/assets/pages/'. app()->getLocale() .'/help.md');
		
		$help_file_content = file_get_contents( @is_file($path) ? $path : $fallback );

		$page_text = \Markdown::convertToHtml($help_file_content);

		return view('static.page', [
			'pagetitle' => trans('pages.help'),
			'page_content' => $page_text]);
	}

	public function importhelp()
	{
		
		$help_file_content = file_get_contents( base_path('resources/assets/pages/import.md') );

		$page_text = \Markdown::convertToHtml($help_file_content);

		return view('static.page', ['page_title' => trans('pages.help'), 'page_content' => $page_text]);
	}

	public function contact()
	{

		$inst = $this->adapter->getInstitution(\Config::get('dms.institutionID'));

		$since = localized_date_human_diff($inst->created_at);

		$geocode = $this->geoCodeCity($inst->address_locality, $inst->address_country);

		$page_title = trans('pages.contact');
        
		return view('static.contact', compact('inst', 'since', 'geocode', 'page_title'));
	}




	// TODO: refactor - geoCodeCity method cannot live in a Controller 
	private function geoCodeCity($city, $country)
	{
		//http://nominatim.openstreetmap.org/search?q=bishkek,kyrgyzstan&format=json

		$slug = str_slug($city . '-' . $country, '-');

		$value = \Cache::rememberForever('geocode_' . $slug, function() use($city, $country)
		{
			try{
				$headers = array(
					'timeout' => 60
				);
				
				$http = new HttpClient($headers);

				
				$result = $http->request( 'GET', 'http://nominatim.openstreetmap.org/search?q=' . $city. ',' . $country . '&format=json');


				$decoded = json_decode( $result->getBody() );

				if(!empty($decoded)){
					$decoded = $decoded[0];

					return array('lat' => $decoded->lat, 'lon' => $decoded->lon);
				}
				else {
					return array('lat' => "42.8766343", 'lon' => "74.6070116");
				}

				
			}catch(Exception $ex){

				\Log::error('geoCodeCity error', array('param' => compact('city', 'country'), 'exception' => $ex));

				return array('lat' => "42.8766343", 'lon' => "74.6070116");
			}

		});

		return $value;
	}
}
