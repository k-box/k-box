<?php

namespace Klink\DmsAdapter;

/**
 * Define the available types of document visibility.
 *
 * @package Klink
 */
final class KlinkVisibilityType
{

	/**
	 * Private document
	 * 
	 * The document will be available only to the institution that uploaded it
	 */	
	const KLINK_PRIVATE = 'private';

	/**
	 * Public document.
	 * 
	 * The document will be visibile to all the institution in KLink
	 */
	const KLINK_PUBLIC = 'public';


	/**
	 * Perform a parse of the given string into a visibility constant
	 * @param string $string the value to be transformed into a KlinkVisibilityType
	 * @return KlinkVisibilityType
	 * @throws InvalidArgumentException if the passed string is not a valid visibility
	 */
	public static function fromString( $string ){

		if( $string === self::KLINK_PRIVATE ){
			return KlinkVisibilityType::KLINK_PRIVATE;
		}
		elseif ( $string === self::KLINK_PUBLIC ) {
			return KlinkVisibilityType::KLINK_PUBLIC;
		}

		throw new InvalidArgumentException("Wrong enumeration value");
		

	}

}