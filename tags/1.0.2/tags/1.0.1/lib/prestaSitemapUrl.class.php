<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Class used for managing url entites
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class prestaSitemapUrl
{
	const CHANGE_FREQUENCY_ALWAYS	= 'always';
	const CHANGE_FREQUENCY_HOURLY	= 'hourly';
	const CHANGE_FREQUENCY_DAILY	= 'daily';
	const CHANGE_FREQUENCY_WEEKLY	= 'weekly';
	const CHANGE_FREQUENCY_MONTHLY	= 'monthly';
	const CHANGE_FREQUENCY_YEARLY	= 'yearly';
	const CHANGE_FREQUENCY_NEVER	= 'never'; 
	
	
	protected
		$location,				// absolute url
		$lastModificationDate,	// last modifcaiotn date
		$changeFrequency,		// change frequency
		$priority; 				// priority
		
	/**
	 * Construct a new prestaSitemapUrl mainly identified by it's url
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param Mixed $location[optional] - Valid parameters for a call to url_for()
	 * @param DateTime $lastModificationDate[optional]
	 * @param String $changeFrequency[optional]
	 * @param Float $priority[optional]
	 */	
	public function __construct( $location = null, DateTime $lastModificationDate = null, $changeFrequency = null, $priority = null )
	{
		// use a callback for the location as $location can be an array of parameters
		$this->setLocation( $location );
		$this->setLastModificationDate( $lastModificationDate );
		$this->setChangeFrequency( $changeFrequency );
		$this->setPriority( $priority );
	}
	
	
	/**
	 * Optimize the size the the serialized version of this object
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @return unknown_type
	 */
	public function toArray()
	{
		// only the '_' var name will appears in the serialized version of this object
		$array	= array();
		$counter	= 0;
		// get the nname => value pair of all object's vars that must be stored in serialize version of this object and store them as a numeric array instead of an associative array 
		foreach( get_object_vars( $this ) as $name => $value )
		{
			$array[ $counter++ ]	= $value;
		}
		return $array;
	}
	
	
	/**
	 * Properly restore the datas when unserialized 
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 */
	public function fromArray( $array )
	{
		$counter	= 0;
		// for each property of this object, we'll restore from numeric array stored in this->_ to correct object's vars
		foreach( get_object_vars( $this ) as $name => $value )
		{
			$this->$name	= $array[ $counter++ ];
		}
	}
	
	/**
	 * Define the location base on url_for method
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param String $internal_uri
	 * @return prestaSitemapUrl
	 */
	public function setLocation( $internal_uri )
	{
		if( !is_null( $internal_uri ) )
		{
			$this->location	= self::toValidUtf8LocationContent( url_for( $internal_uri, true ) );
		}
		else
		{
			$this->location	= null;
		}
		return $this;
	}
	
	/**
	 * Define the location base on url_for1 method
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param String $internal_uri
	 * @return prestaSitemapUrl
	 */
	public function setLocation1( $internal_uri )
	{
		$this->location	= self::toValidUtf8LocationContent( url_for1( $internal_uri, true ) );
		return $this;
	}
	
	/**
	 * Define the location base on url_for2 method
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param String $routeName
	 * @param Mixed $params
	 * @return prestaSitemapUrl
	 */
	public function setLocation2( $routeName, $params = array() )
	{
		$this->location	= self::toValidUtf8LocationContent( url_for2( $routeName, $params, true ) );
		return $this;
	}
	
	/**
	 * return the location
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	
	/**
	 * Define the last modificaiton date of this entry
	 * 
	 * Produce ISO 8601 date string (valid W3C Datetime format)
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param DateTime $lastModificationDate - DateTime object or null used for defining the last modification date of this entry
	 * @return prestaSitemapUrl
	 */
	public function setLastModificationDate( DateTime $lastModificationDate = null )
	{
		if( $lastModificationDate instanceOf DateTime )
		{
			$this->lastModificationDate	= $lastModificationDate->format('c');
		}
		else
		{
			$this->lastModificationDate	= null;
		}
		return $this;
	}

	
	/**
	 * return the last modification date
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getLastModificationDate()
	{
		return $this->lastModificationDate;
	}
		
	
	/**
	 * Define the change frequency of this entry
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param String $changeFrequency - String or null value used for defining the change frequency
	 * @return prestaSitemapUrl
	 */
	public function setChangeFrequency( $changeFrequency )
	{
		static $possibleChangeFrequencyValues	= array(
			self::CHANGE_FREQUENCY_ALWAYS	=> true,
			self::CHANGE_FREQUENCY_HOURLY	=> true,
			self::CHANGE_FREQUENCY_DAILY	=> true,
			self::CHANGE_FREQUENCY_WEEKLY	=> true,
			self::CHANGE_FREQUENCY_MONTHLY	=> true,
			self::CHANGE_FREQUENCY_YEARLY	=> true,
			self::CHANGE_FREQUENCY_NEVER	=> true,
		);
		
		// check that this is a valid frequency
		if( !is_null( $changeFrequency ) && isset( $possibleChangeFrequencyValues[ $changeFrequency ] ) )
		{
			$this->changeFrequency	= $changeFrequency;
		}
		else
		{
			$this->changeFrequency	= null;
		}
		return $this;
	}
	
	/**
	 * return the change frequency
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getChangeFrequency()
	{
		return $this->changeFrequency;
	}
	
	
	/**
	 * Define the priority of this entry
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param Float $priority - Float or null value used for defining the priority
	 * @return prestaSitemapUrl
	 */
	public function setPriority( $priority )
	{
		if( !is_null( $priority ) && is_numeric( $priority ) && $priority >= 0 && $priority <= 1)
		{
			$this->priority	= sprintf( '%01.1f', $priority );
		}
		else
		{
			$this->priority	= null;
		}
		return $this;
	}
	
	
	/**
	 * return the priority
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getPriority()
	{
		return $this->priority;
	}
	
	
	/**
	 * Return the xml content of this object
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 24 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 24 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function toXML()
	{
		ob_start();
?>
	<url>
		<loc><?php echo $this->getLocation() ?></loc>
	<?php if( !is_null( $this->getLastModificationDate() ) ): ?>
		<lastmod><?php echo $this->getLastModificationDate() ?></lastmod>
	<?php endif; ?>
	<?php if( !is_null( $this->getChangeFrequency() ) ): ?>
		<changefreq><?php echo $this->getChangeFrequency() ?></changefreq>
	<?php endif; ?>
	<?php if( !is_null( $this->getPriority() ) ): ?>
		<priority><?php echo $this->getPriority() ?></priority>
	<?php endif; ?>
	</url>
<?php
		return ob_get_clean();
	}
	
	/**
	 * Convert datas to utf-8, encode special xml characters, and refuse string length >= 2048
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @param $string
	 * @return String
	 */
	protected static function toValidUtf8LocationContent( $string )
	{
		if( !is_null( $string ) )
		{
			// try to convert to UTF-8 if 'mb_convert_encoding' is available
			$string	= function_exists( 'mb_convert_encoding' ) ? mb_convert_encoding( $string, 'UTF-8', 'auto' ) : $string;
			
			// convert string and encode specials htmlcharacters (doesn't encode already encoded characters)
			$string	= htmlspecialchars( $string, ENT_QUOTES, 'UTF-8', false );
			
			$length	= function_exists( 'mb_strlen' ) ? mb_strlen( $string, 'UTF-8' ) : strlen( $string );
			if( $length >= 2048 )
			{
				$string	= null;
			}
		}
		return $string;
	}
}