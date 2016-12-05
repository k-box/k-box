<?php namespace Klink\DmsAdapter;

use KlinkDMS\Institution;
use KlinkDMS\Option;
use Illuminate\Support\Collection;

/**
* 
*/
class KlinkAdapter
{
	
	/**
	 * [$klink_config description]
	 * @var KlinkConfiguration
	 */
	private $klink_config = null;

	/**
	 * Client configured for connecting to the institution's K-Link Core
	 * @var \KlinkCoreClient
	 */
	private $connection = null;



	private $documentTypes = array('document', 'presentation' , 'spreadsheet', 'image', 'web-page' );


	function __construct( )
	{

		$cores = array(
			new \KlinkAuthentication(\Config::get('dms.core.address'), \Config::get('dms.core.username'), \Config::get('dms.core.password'), \KlinkVisibilityType::KLINK_PRIVATE)
		);
		
		try {
			
			$can_read_options = true;

			if(Option::option(Option::PUBLIC_CORE_ENABLED, false) && Option::option(Option::PUBLIC_CORE_CORRECT_CONFIG, false)){
				
				try{
				
					$cores[] = new \KlinkAuthentication(Option::option(Option::PUBLIC_CORE_URL), Option::option(Option::PUBLIC_CORE_USERNAME), @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD)), \KlinkVisibilityType::KLINK_PUBLIC);
					
				}catch(\Exception $e){
					//TODO: launch some kind of events so the admin can see what happened
					
					\Log::error('Public Core configuration error', array('exception' => $e));
					Option::put(Option::PUBLIC_CORE_ENABLED, false);
				}
			}
		
		}catch(\Exception $qe){
			$can_read_options = false;
			\Log::warning('Exception while reading K-Link Public core settings', array('exception' => $qe));
		}


		$this->klink_config = new \KlinkConfiguration(\Config::get('dms.institutionID'), \Config::get('dms.identifier'), $cores);
		
		if($can_read_options && Option::option(Option::PUBLIC_CORE_DEBUG, false)){
			$this->klink_config->enableDebug();
		}

		$this->connection = new \KlinkCoreClient( $this->klink_config, app('log') );
	}


	public function invokeSomething()
	{

		$inst = $this->connection->getInstitution('CA');


		return $inst->name;
	}
    
    
    public function isKlinkPublicEnabled(){
        return !!Option::option(Option::PUBLIC_CORE_ENABLED, false);
    }


	public function getConnection()
	{
		return $this->connection;
	}



	public function testNetworkConnectivity()
	{

		$error = null;
		$health = null;
		$result = \KlinkCoreClient::test( $this->klink_config, $error, false, $health );

		return compact('result', 'error');
	}
	
	public function testExplicitNetworkConnectivity($url, $username, $password)
	{

		$cores = array(
			new \KlinkAuthentication($url, $username, $password, \KlinkVisibilityType::KLINK_PUBLIC)
		);

		$conf = new \KlinkConfiguration(\Config::get('dms.institutionID'), \Config::get('dms.identifier'), $cores);

		$error = null;
		$health = null;
		$result = \KlinkCoreClient::test( $conf, $error, false, $health );

		return compact('result', 'error');
	}

	/**
	 * Retrieve an institution given its K-Link identifier
	 * @param  string $klink_id the K-Link Id
	 * @return \KlinkDMS\Institution|null the instance of Institution that corresponds to the given id or null if the institution is unknown or the id is not valid
	 */
	public function getInstitution( $klink_id )
	{
		$cached = Institution::findByKlinkID( $klink_id );
		
		if(is_null($cached)){

			try{

				$core_inst = $this->connection->getInstitution( $klink_id );

				$cached = Institution::fromKlinkInstitutionDetails( $core_inst );

			}catch(\Exception $e){

				\Log::error('Error get Institution from K-Link', array('context' => 'KlinkAdapter::getInstitution', 'param' => $klink_id, 'exception' => $e));

				return $klink_id;
			}
		}

		return $cached;
	}

	/**
	 * Get the institutions name given the K-Link Identifier
	 * @param  string $klink_id The K-Link institution identifier
	 * @return string|boolean           The name of the institution if exists, otherwise false (false will be returned also in case of error)
	 */
	public function getInstitutionName( $klink_id )
	{

		$cached = $this->getInstitution( $klink_id );

		return is_null($cached) ? $klink_id : $cached->name;
	}

	/**
	 * Get all the institutions currently available in the network.
	 *
	 * This method also synchronize the cache of the institutions with the current info coming from the network
	 * 
	 * @param  array  $columns [description]
	 * @return [type]          [description]
	 */
	public function getInstitutions($columns = array('*'), $forceSync = false)
	{
		$cached = Institution::all($columns);
		
		$connection = $this->connection;
		
		$insts = \Cache::remember('dms_institutions', 1, function() use($connection, $cached, $columns)
		{
			
			try{

				$core_insts = $connection->getInstitutions();

				foreach ($core_insts as $inst) {
					Institution::fromKlinkInstitutionDetails( $inst );	
				}

				return Institution::all($columns);

			}catch(Exception $e){

				\Log::error('Error get Institutions from K-Link', array('context' => 'KlinkAdapter::getInstitutions', 'exception' => $e));

				return $cached;
			}
			
		});
		
		
		if(!is_null($insts) && !$insts->isEmpty()){

			return $insts;
			
		}
		
		return $cached;

	}
	
	/**
		Save the institution details on the Core
	*/
	public function saveInstitution(Institution $institution){
		$this->connection->saveInstitution($institution->toKlinkInstitutionDetails());
	}
	
	public function deleteInstitution(Institution $institution){
		$this->connection->deleteInstitution($institution->klink_id);
	}

	/**
	 * Get the number of institutions currently in the K-Link Network.
	 * 
	 * @return int the number of institutions. Can be zero in case of errors
	 */
	public function getInstitutionsCount()
	{
		return Institution::count();
	}
	
	/**
	 * Returns the number of indexed documents with the respect to the visibility.
	 *
	 * Public visibility -> all documents inside the K-Link Network
	 *
	 * private visibility -> documents inside institution K-Link Core
	 *
	 * This method uses caching, so be aware that the results you receive might be older than real time
	 * 
	 * @param  string $visibility the visibility (if nothing is specified, a 'public' visibility is considered)
	 * @return integer            the amount of documents indexed
	 */
	public function getDocumentsCount($visibility = 'public')
	{

        if(!$this->isKlinkPublicEnabled() && $visibility==='public'){
            return 0;
        }

		try{

			$conn = $this->connection;

			$value = \Cache::remember($visibility . '_documents_count', 15, function() use($conn, $visibility)
			{
				\Log::info('Updating documents count cache for ' . $visibility);
				
				$res = $conn->search( '*', $visibility, 0, 0 );

				return $res->getTotalResults();
			});
			
			return $value;

		}catch(\Exception $e){

			\Log::error('Error getDocumentsCount', array('visibility' => $visibility, 'exception' => $e));

			return 0;

		}


		
	}

	/**
	 * Returns the documents statistics aggregated for public and private
	 * 
	 * @return [type] [description]
	 */
	public function getDocumentsStatistics()
	{
		$conn = $this->connection;

		if(!\Cache::has('dms_documents_statististics')){

			$fs = \KlinkFacetsBuilder::create()->documentType()->build();

            $public_facets_response = array();

			$private_facets_response = $conn->facets( $fs, 'private' );

			$stats = $this->compactFacetResponse($public_facets_response, $private_facets_response);

			\Cache::put('dms_documents_statististics', $stats, 60);

		}
		
		return \Cache::get('dms_documents_statististics');
	}


	private function mapFacetItemToKeyValue(\KlinkFacetItem $item){

		return array( $item->getTerm() => $item->getOccurrenceCount() );

	}


	private function compactFacetResponse($public_response, $private_response){

		// array_flatten(array_fetch($public_response, 'items'))));

		$public = $this->getL2Keys(array_map( array($this, 'mapFacetItemToKeyValue'), array_flatten(array_fetch($public_response, 'items'))));

		// the idea is document => count

		$private = $this->getL2Keys(array_map( array($this, 'mapFacetItemToKeyValue'), array_flatten(array_fetch($private_response, 'items'))));

		$all = array();

		foreach ($this->documentTypes as $type) {

			$pu = isset($public[$type]) ? $public[$type] : 0;
			$pr = isset($private[$type]) ? $private[$type] : 0;
			$to = $pu + $pr;

			$all[$type]['public'] = $pu;
			$all[$type]['private'] = $pr;
			$all[$type]['total'] = $to;

		}

		return $all;

	}

	private function getL2Keys($array)
	{
	    $result = array();
	    foreach($array as $sub) {
	        $result = array_merge($result, $sub);
	    }        
	    return $result;
	    // return array_keys( $result);
}
}