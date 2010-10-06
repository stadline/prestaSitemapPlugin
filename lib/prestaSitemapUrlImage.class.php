<?php

/*
 * This file is part of the prestaSitemaPlugin package.
 * (c) Christophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Class used for managing image's url entites
 * 
 * @author	Alain Flaus <aflaus@prestaconcept.net>
 * @version	SVN: $Id$ 1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
 */
class prestaSitemapUrlImage
{	
	protected
		$location,				// absolute url
		$caption,				// alt
		$geo_location,
		$title,
		$license;

		
	/**
	 * Construct a new prestaSitemapUrlImage mainly identified by it's url
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	Mixed $location[optional] - Valid parameters for a call to image_path()
	 * @param 	String $caption[optional]
	 * @param 	String $geo_location[optional]
	 * @param 	String $title[optional]
	 * @param 	String $license[optional]
	 */
	public function __construct( $location = null, $caption = null, $geo_location = null, $title = null, $license = null )
	{
		// use a callback for the location as $location can be an array of parameters
		$this->setLocation( $location );
		$this->setCaption( $caption );
		$this->setGeoLocation( $geo_location );
		$this->setTitle( $title );
		$this->setLicense( $license );
	}
	
	
	/**
	 * Optimize the size the the serialized version of this object
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @return 	unknown_type
	 */
	public function toArray()
	{
		// only the '_' var name will appears in the serialized version of this object
		$array	= array();
		$counter	= 0;
		// get the name => value pair of all object's vars that must be stored in serialize version of this object and store them as a numeric array instead of an associative array 
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
	 * Define the location base on image_path method
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param 	String $internal_uri
	 * @return 	prestaSitemapUrlImage
	 */
	public function setLocation( $source )
	{
		if( !is_null( $source ) )
		{
			$this->location	= self::toValidUtf8LocationContent( image_path( $source, true ) );
		}
		else
		{
			$this->location	= null;
		}
		return $this;
	}
	
	
	/**
	 * Define the location base on url_for method
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	$source
	 * @return 	prestaSitemapUrlImage
	 */
	public function setLocationUrlFor( $source )
	{
		if( !is_null( $source ) )
		{
			$this->location	= self::toValidUtf8LocationContent( url_for( $source, true ) );
		}
		else
		{
			$this->location	= null;
		}
		return $this;
	}
	
	
	/**
	 * return the location
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return 	String
	 */
	public function getLocation()
	{
		return $this->location;
	}
	
	
	/**
	 * Define the caption
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $caption
	 * @return 	prestaSitemapUrlImage
	 */
	public function setCaption( $caption )
	{
		$this->caption = self::toValidUtf8LocationContent($caption);
		
		return $this;
	}
	
	
	/**
	 * Return the caption
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getCaption()
	{
		return $this->caption;
	}
	
	
	/**
	 * Define the geo_location
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $caption
	 * @return 	prestaSitemapUrlImage
	 */
	public function setGeoLocation( $geo_location )
	{
		$this->geo_location = self::toValidUtf8LocationContent($geo_location);
		
		return $this;
	}
	
	
	/**
	 * Return the geo_location
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getGeoLocation()
	{
		return $this->geo_location;
	}
	
	
	/**
	 * Define the title
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $title
	 * @return 	prestaSitemapUrlImage
	 */
	public function setTitle( $title )
	{
		$this->title = self::toValidUtf8LocationContent($title);
		
		return $this;
	}
	
	
	/**
	 * Return the title
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Define the license
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @param 	String $license
	 * @return 	prestaSitemapUrlImage
	 */
	public function setLicense( $license )
	{
		$this->license = self::toValidUtf8LocationContent($license);
		
		return $this;
	}
	
	
	/**
	 * Return the license
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function getLicense()
	{
		return $this->license;
	}
	
	
	/**
	 * Return the xml content of this object
	 * 
	 * @author	Alain Flaus <aflaus@prestaconcept.net>
	 * @version	1.0 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @since	5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 * @return 	String
	 */
	public function toXML()
	{
		ob_start();
?>
	<image:image>
		<image:loc><?php echo $this->getLocation() ?></image:loc>
	<?php if( !is_null( $this->getCaption() ) ): ?>
		<image:caption><?php echo $this->getCaption() ?></image:caption>
	<?php endif; ?>
	<?php if( !is_null( $this->getGeoLocation() ) ): ?>
		<image:geo_location><?php echo $this->getGeoLocation() ?></image:geo_location>
	<?php endif; ?>
	<?php if( !is_null( $this->getTitle() ) ): ?>
		<image:title><?php echo $this->getTitle() ?></image:title>
	<?php endif; ?>
	<?php if( !is_null( $this->getLicense() ) ): ?>
		<image:license><?php echo $this->getLicense() ?></image:license>
	<?php endif; ?>
	</image:image>
<?php
		return ob_get_clean();
	}
	
	
	/**
	 * Convert datas to utf-8, encode special xml characters, and refuse string length >= 2048
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @param 	$string
	 * @return 	String
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