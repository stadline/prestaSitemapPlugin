<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Christophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Manage generation of groups of urls
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class prestaSitemapSection
{
	const VALID_SECTION_NAME_PATTERN	= '@^[0-9a-zA-Z]+$@';
	
	protected
		$name,							// section name
		$a_cacheParams,					// array of cache params
		$expirationDate,				// expiration date
		$isUpToDate,					// Boolean indicating that the datas in cache are up-to-date for this section
		$a_o_sitemapUrls	= array(),	// Array of associated prestaSitemapUrl objects 
		$stackTrace,					// Stack trace fo the function call that identifiy this section
		$o_sitemapGenerator;			// Reference to prestaSitemapGenerator instance
	
	/**
	 * Constructor for the sitemapSection
	 * 
	 * Valid options:
	 * 	- lifetime		: Integer: The cache lifetime in seconds
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param String $sectionName[optionnal] - Section name
	 * @param array $a_options - Array of supperted options
	 */
	public function __construct( $sectionName = 'main', Array $a_options = array() )
	{
		// check sectionName validity
		if( !preg_match( self::VALID_SECTION_NAME_PATTERN, $sectionName ) )
		{
			throw new Exception( 'The section name must match the following pattern "'. self::VALID_SECTION_NAME_PATTERN .'"' );
		}
		
		$this->name	= $sectionName;
		
		// define cache datas
		$a_defaultCache			= sfConfig::get('app_prestaSitemapPlugin_sectionCache');
		$this->a_cacheParams	= array(
			'enabled'	=> $a_defaultCache['enabled'],
			'lifetime'	=> array_key_exists( 'lifetime', $a_options ) ? $a_options['lifetime'] : $a_defaultCache['lifetime']
		);
		
		
		// get the stack trace in order to identify this section execution's context
		$e = new Exception();
		$this->stackTrace	= $e->getTraceAsString();
		
		
		// Notify that a new sitemap section has been created
		$dispatcher		= sfContext::getInstance()->getEventDispatcher();
		$this->o_sitemapGenerator	= $dispatcher->filter( new sfEvent( $this, 'presta_sitemap.new_sitemap_section' ), null )->getReturnValue();
		if( !( $this->o_sitemapGenerator instanceOf prestaSitemapGenerator ) )
		{
			throw new Exception( "Miss sitemapGenerator response to 'presta_sitemap.new_sitemap_section' event" );
		}
		
		// *************
		// *** ask for cached datas
		// *************
		
		$a_cacheDatas	= $this->o_sitemapGenerator->getInternalCacheDatas( $this->getSectionId() );
		
		// cache is valid
		if( !is_null( $a_cacheDatas ) )
		{
			$this->isUpToDate		= true;
			$this->expirationDate	= $a_cacheDatas['expirationDate'];
			$this->a_o_sitemapUrls	= array_key_exists( 'a_o_sitemapUrls', $a_cacheDatas ) ? $a_cacheDatas['a_o_sitemapUrls'] : array();
			
			if( !is_array( $this->a_o_sitemapUrls ) )
			{
				$this->a_o_sitemapUrls	= array();
			}
		}
		else
		{
			$this->isUpToDate		= false;
			$this->a_o_sitemapUrls	= array();
			$this->expirationDate	= $this->a_cacheParams['enabled'] ? gmdate( 'Y-m-d H:i:s', time() + $this->a_cacheParams['lifetime'] ) : '0000-00-00 00:00:00';
		}
		// *************
	}
	
	
	/**
	 * Return a unique identifier for this section
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 24 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 24 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getSectionId()
	{
		return $this->name .'.'. md5( $this->stackTrace );
	}
	
	
	/**
	 * Return the new expiration date
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getExpirationDate()
	{
		return $this->expirationDate;
	}
	
	
	/**
	 * Add a sitemap url to the whole results
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param prestaSitemapUrl $o_sitemapUrl - The object used for defining the sitemap entry
	 */
	public function addUrl( prestaSitemapUrl $o_sitemapUrl )
	{
		$this->a_o_sitemapUrls[]	= $o_sitemapUrl;
	}	
	
	
	/**
	 * Return the prestaSitemapUrl object associated to this section object 
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return Array of prestaSitemapUrl objects
	 */
	public function getUrls()
	{
		return $this->a_o_sitemapUrls;
	}
	
	
	/**
	 * Return the number of urls of this section
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return Integer
	 */
	public function countUrls()
	{
		return count( $this->a_o_sitemapUrls );
	}
	
	
	/**
	 * Indicate where this section is up-to-date in cache or not
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @return Boolean
	 */
	public function isUpToDate()
	{
		return $this->isUpToDate;
	}
	
	/**
	 * Indicate if the section has just changed
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return Boolean
	 */
	public function hasChanged()
	{
		return $this->hasChanged;
	}
	
	/**
	 * Return the section name
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Return the stack trace identifier for this object
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @return String
	 */
	public function getStackTrace()
	{
		return $this->stackTrace;	
	}
	
	/**
	 * Delete empty sitemap urls from the urls associated to this section
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.1 - 5 oct. 2010 - Alain Flaus <aflaus@prestaconcept.net>
	 */
	public function deleteEmptyUrls()
	{
		foreach( $this->a_o_sitemapUrls as $key => $o_sitemapUrl )
		{
			if( is_null( $o_sitemapUrl->getLocation() ) )
			{
				unset( $this->a_o_sitemapUrls[ $key ] );
				unset( $o_sitemapUrl );
			}
			else
			{
				$o_sitemapUrl->deleteEmptyUrls();
			}
		}
	}
}