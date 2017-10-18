<?php

namespace Klink\DmsAdapter;

use InvalidArgumentException;
use KlinkDMS\Traits\HasEnums;

/**
* Available search aggregations.
*/
class KlinkFacets
{
	use HasEnums;

	const MIME_TYPE = 'properties.mime_type';

	const LANGUAGE = 'properties.language';

	const UPLOADER = 'uploader.name';

	const COLLECTIONS = 'properties.collection';
	
	const PROJECTS = 'properties.tag'; // using tags as there are no dedicated fields for projects

	const CREATED_AT = 'properties.created_at';

	const UPDATED_AT = 'properties.updated_at';

	const SIZE = 'properties.size';

	const COPYRIGHT_OWNER_NAME = 'copyright.owner.name';
	
	const COPYRIGHT_USAGE_SHORT = 'copyright.usage.short';
}
