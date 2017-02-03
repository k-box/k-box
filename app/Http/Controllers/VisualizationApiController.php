<?php namespace KlinkDMS\Http\Controllers;

use KlinkDMS\Http\Requests;
use KlinkDMS\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

use Illuminate\Http\Request;

class VisualizationApiController extends Controller {


	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $service = null;
	private $searchService = null;

	public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapter, \Klink\DmsSearch\SearchService $searchService)
	{
		$this->service = $adapter;
		$this->searchService = $searchService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(AuthGuard $auth, \Request $request)
	{
		$search_terms = $request::input('s', '*');
		
		if(empty($search_terms)){
			$search_terms = '*';
		}

		$visibility = $request::input('visibility', 'public');

		$res = $this->service->search( '*', $visibility, 0, 0 );

		$grand_total = $res->getTotalResults();

		$limit = $grand_total;

		$facets = array();

		$default_facets_names = ['documentType', 'institutionId', 'language'];

		$fs_builder = \KlinkFacetsBuilder::create();

		$current_filters = null;

		$param_fs = $request::input('fs', null);
		$empty_fs = empty($param_fs);

		if($request::has('fs') && !$empty_fs){
			$fs_names = \KlinkFacetsBuilder::allNames(); //check what we have in the parameters

			// also parameter validation

			$current_names = [];

			$param_inner_fs = null;
			$empty_inner_fs = true;
			foreach ($fs_names as $fs) {
				$param_inner_fs = $request::input($fs, null);
				$empty_inner_fs = empty($param_inner_fs);
				if($request::has($fs) && !$empty_inner_fs){
					$current_names[] = $fs;
					// ok valid facet

					$filter_value = $request::input($fs);

					$fs_builder->{$fs}($filter_value, $grand_total, 0);

					$current_filters[$fs] = explode(',', $filter_value);
				}
			}

			$default_facets_names = array_diff($default_facets_names, $current_names);
		}

		// what default facets are missing? we need to add it
		foreach ($default_facets_names as $fs) {
			
			$fs_builder->{$fs}(0);
			
		}

		$facets_to_apply = $fs_builder->build();

		$results_from_the_core = $this->service->search( $search_terms, $visibility, $limit, 0, $facets_to_apply );
		
		$results_from_the_core->facets = $this->searchService->limitFacets($results_from_the_core->getFacets());


		foreach ($results_from_the_core->items as $res) {
			
			$institution = $this->service->getInstitution( $res->institutionID );

			if(!is_null($institution)){
				$res->document_descriptor->institutionName = $institution->name;
			}

			$res->document_descriptor->creationDate = \Carbon\Carbon::createFromFormat( \DateTime::RFC3339, $res->creationDate)->formatLocalized('%A %d %B %Y');

			unset($res->document_descriptor->authors);
			unset($res->document_descriptor->userUploader);
			unset($res->document_descriptor->abstract);
			unset($res->document_descriptor->hash);
			unset($res->document_descriptor->topicTerms);
			unset($res->document_descriptor->documentFolders);
			unset($res->document_descriptor->documentGroups);
			unset($res->document_descriptor->titleAliases);
			unset($res->document_descriptor->userOwner);
			unset($res->document_descriptor->mimeType);

		}

			if(!is_null($results_from_the_core)){
				return response()->json($results_from_the_core);
			}
			else {
				return response('Error', 500);
			}

		// dd($results_from_the_core);


		
		
		// // $fake = false;
		// if(empty($search_terms)){
		// 	// showing search page without query, let's request only the facets
		// 	$search_terms = '*';
			
		// }


		// $default_facets = \KlinkFacetsBuilder::create()->institution()->documentType()->language()->build(); // usefull for the elastic list, so we setup always that

		
		
		
	}

	
}
