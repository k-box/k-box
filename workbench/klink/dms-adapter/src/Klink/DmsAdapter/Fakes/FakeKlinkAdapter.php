<?php namespace Klink\DmsAdapter\Fakes;

use KlinkDMS\Institution;
use KlinkDMS\Option;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Support\Collection;

use Klink\DmsAdapter\Contracts\KlinkAdapter as AdapterContract;

use KlinkCoreClient;
use KlinkAuthentication;
use KlinkConfiguration;
use KlinkSearchResult;
use KlinkSearchResultItem;
use KlinkHelpers;
use Config;
use KlinkDocument;
use KlinkDocumentDescriptor;
use KlinkVisibilityType;
use KlinkFacetsBuilder;
use KlinkFacetItem;

use Faker\Factory as FakerFactory;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * FakeKlinkAdapter. Simulates a KlinkAdapter that always return positive responses. 
 *
 * This might be useful in case you want to know if a method is called
 */
class FakeKlinkAdapter implements AdapterContract
{
	
	/**
	 * @var array
	 */
	private $calls;

	/**
	 * @var Faker\Generator
	 */
	private static $faker;

	/**
	 * Creates a new FakeKlinkAdapter instance.
	 */
	function __construct( )
	{
		$this->calls = [];
		self::$faker = FakerFactory::create();
	}
    
	/**
     * Check if the network configuration is enabled
     *
     * @return bool
	 * @uses Option::PUBLIC_CORE_ENABLED
     */
	public function isNetworkEnabled()
	{
        return !!Option::option(Option::PUBLIC_CORE_ENABLED, false);
	}


	/**
	 * 
	 * 
	 * @uses \KlinkCoreClient::test
	 * @return array containing a key result and error, the key result contains the return 
	 *               value from {@see \KlinkCoreClient::test}, while the key error contains 
	 *               the eventual exception if the test fails
	 */
	public function test(KlinkAuthentication $core = null)
	{

		$error = null;
		$result = true;

		return compact('result', 'error');
	}

	/**
     * Get the registered Institutions
     *
     * @param string $id The K-Link ID of the institution to find. Default null, all known institutions are returned
     * @param mixed $default The default value to return in case the requested institution cannot be found. This parameter is ignored if $id is null
     * @return Collection|Institution|null the known institutions. If the $id is passed the single institution is returned, if found
     */
    public function institutions($id = null, $default = null)
	{
		
		if(!is_null($id) && is_null(KlinkHelpers::is_valid_id($id, 'id')))
		{
			$cached = Institution::where('klink_id', $id)->first();

			return !is_null($cached) ? $cached : factory(Institution::class)->create(['klink_id' => $id]);
		}
		else {
			return factory(Institution::class, 5)->create();
		}
	}

	/**
	 * Get the institutions name given the K-Link Identifier
	 * @param  string $klink_id The K-Link institution identifier
	 * @return string           The name of the institution if exists, otherwise the passed id is returned
	 */
	public function getInstitutionName( $klink_id )
	{
		return $klink_id;
	}

	
	/**
	 * Save the institution details on the K-Link Network
	 *
	 * @param Institution $institution the institution to save
	 */
	public function saveInstitution(Institution $institution)
	{
		// $this->connection->saveInstitution($institution->toKlinkInstitutionDetails());
	}
	
	/**
	 * Delete the institution details from the K-Link Network.
	 *
	 * The institution is deleted according to the klink_id field value
	 *
	 * @param Institution $institution the institution to save
	 */
	public function deleteInstitution(Institution $institution)
	{
		// $this->connection->deleteInstitution($institution->klink_id);
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
	 * @return integer            the amount of documents indexed. It returns 0 also if the public network is 
	 *                            not enabled, but the public visibility is requested
	 */
	public function getDocumentsCount($visibility = 'public')
	{

		return 24;
        
	}

	/**
	 * Returns some documents statistics, like document types, aggregated 
	 * for public and private
	 * 
	 * @return array 
	 */
	public function getDocumentsStatistics()
	{
		$all = [
			'document' => [
				'public' => 5
			],
			'document' => [
				'private' => 5
			],
			'document' => [
				'total' => 10
			],
		];
		
		return $all;
	}



	public function search($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null)
	{
		return self::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);
	}

	public function facets( $facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*' )
	{
		return self::generateFacetsResponse($facets, $visibility, $term);
	}

	public function getDocument( $institutionId, $documentId, $visibility = KlinkVisibilityType::KLINK_PRIVATE )
	{
		//TODO: return a Document Descriptor
		return null; // $this->connection->getDocument( $institutionId, $documentId, $visibility );
	}

	public function updateDocument( KlinkDocument $document )
	{
		$this->countIndexing($document->getDescriptor()->getLocalDocumentID());

		return $document->getDescriptor();
	}

	public function removeDocumentById( $institution, $document, $visibility = KlinkVisibilityType::KLINK_PRIVATE )
	{
		
		return true;
	}

	public function removeDocument( KlinkDocumentDescriptor $document )
	{
		return $this->removeDocumentById($document->getInstitutionID(), $document->getLocalDocumentID(), $document->getVisibility());
	}

	public function addDocument( KlinkDocument $document )
	{
		$this->countIndexing($document->getDescriptor()->getLocalDocumentID());

		return $document->getDescriptor();
	}

	public function generateThumbnailOfWebSite($url, $image_file = null)
	{
		return $this->connection->generateThumbnailOfWebSite($url, $image_file = null);
	}

	public function generateThumbnailFromContent( $mimeType, $data )
	{
		return $this->connection->generateThumbnailFromContent( $mimeType, $data );
	}



	public static function generateSearchResponse($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null)
	{
		
		if(!static::$faker)
		{
			static::$faker = FakerFactory::create();
		}
		
		$res = new KlinkSearchResult($terms, 1, $resultsPerPage*2, $resultsPerPage);

		$res->setVisibility($type);

		$res->startResult = $offset;
		$res->numResults = $resultsPerPage;
		$res->facets = !is_null($facets) ? self::generateFacetsResponse($facets) : self::generateFacetsResponse(KlinkFacetsBuilder::all()->build());


		$fakeResults = [];
		$fakeResultItem = null;

		for ($i=0; $i < $resultsPerPage; $i++) { 

			$fakeResultItem = new KlinkSearchResultItem();
			$fakeResultItem->score = self::$faker->randomFloat(2, 0, 1);
			$fakeResultItem->document_descriptor = KlinkDocumentDescriptor::create(
				'KLINK', 
				substr(self::$faker->sha256, 0, 6), 
				self::$faker->sha256 . self::$faker->sha256, 
				self::$faker->sentence, 
				self::$faker->mimeType, 
				self::$faker->url, 
				self::$faker->url, 
				self::$faker->safeEmail, 
				self::$faker->safeEmail, 
				$type);

			$fakeResults[] = $fakeResultItem;
		}

		$res->items = $fakeResults;

		return $res;
	}

	public static function generateFacetsResponse($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*')
	{

		if(!static::$faker)
		{
			static::$faker = FakerFactory::create();
		}

		$facetItem = null;

		foreach ($facets as $facet) {

			$facetItem = new KlinkFacetItem();
			$facetItem->term = static::$faker->name;
			$facetItem->count = static::$faker->randomDigit;

			$facet->items = [$facetItem];
		}

		return $facets;
	}


	/**
	 * @param string $doc
	 */
	private function countIndexing($doc)
	{
		$indexing = isset($this->calls['indexing']) ? $this->calls['indexing'] : [];

		$indexing[] = $doc;

		$this->calls['indexing'] = $indexing;
	}

	/**
	 * Assert that a document identified by a K-Link ID has been added or updated
	 *
	 * @param string $documentId the descriptors' Local Document Id
	 * @param int $times if more than 1 assert that the same document has been subject to add or update $times times
	 */
	public function assertDocumentIndexed($documentId, $times = 1)
	{

		PHPUnit::assertNotEmpty($this->calls['indexing'], "KlinkAdapter addDocument or updateDocument not called");

		$filtered = array_filter($this->calls['indexing'], function($el) use($documentId){
			return $el === $documentId;
		});

		PHPUnit::assertCount($times,

            $filtered,

            "The expected [{$documentId}] document was not indexed $times times."

        );

	}

}