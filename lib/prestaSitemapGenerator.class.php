<?php
/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) Christophe DOLIVET <cdolivet@prestaconcept.net>
 * (c) Mikael RANDY <mrandy@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * Main class used for generating sitemap
 * 
 * This class is only intend to be used by prestaSitemap plugin. You are not supposed to used this object diretcly
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
 */
class prestaSitemapGenerator
{
	protected
		$a_o_sitemapSections	= array(),	// Array of prestaSitemapSection objects 
		$o_internalCache,					// store internal url objects groupes by section
		$o_generatedCache,					// store generated xml
		$sitemapIndexContent,				// generated index sitemap content
		$isUpToDate,						// indicate wherever generated cached datas are up-to-date
		$a_errorMessages		= array()	// Array of error messages
		;					


	/**
	 * Constructor
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 */	
	public function __construct()
	{
		// load helper as we'll use partial rendering and probably url_for methods
		sfContext::getInstance()->getConfiguration()->loadHelpers( array( 'Partial', 'Url', 'Asset' ) );
		
		
		// *************
		// *** instanciate internal cache layers
		// *************
		$rootCacheDir	= sfConfig::get('app_prestaSitemapPlugin_rootCacheDir') . '/' . $this->getExecutionStringIdentifierFromUrl();
				
		// get config from stroage class
		$storageConfig	= sfConfig::get('app_prestaSitemapPlugin_storage');
		$storageClass				= array_key_exists( 'class', $storageConfig ) ? $storageConfig['class'] : 'sfFileCache';
		$storageDefaultParameters	= array_key_exists( 'param', $storageConfig ) ? $storageConfig['param'] : array();
															
		// define long term cache as the cache cleanup will be managed by the prestaSitemapGenerator
		$this->o_internalCache		= new $storageClass(	array_merge( $storageDefaultParameters, array(
																'cache_dir'	=> $rootCacheDir.'/internal',
																'prefix'	=> $rootCacheDir.'/internal',
																'lifetime'	=> 60*60*24*365*3
															) ) );
		$this->o_generatedCache		= new $storageClass(	array_merge( $storageDefaultParameters, array(
																'cache_dir' => $rootCacheDir.'/generated',
																'prefix' 	=> $rootCacheDir.'/generated',
																'lifetime'	=> 60*60*24*365*3
															) ) );
		// *************
		
														
		// *************
		// *** Connect this generator with the 'presta_sitemap.new_sitemap_section' event
		// *************
		
		$dispatcher			= sfContext::getInstance()->getEventDispatcher();
		$dispatcher->connect( 'presta_sitemap.new_sitemap_section', array( $this, 'addNewSitemapSectionEvent' ) );
		// *************
		
		
		// *************
		// *** Try to load generated cached datas
		// *************
		
		$a_cacheDatas	= unserialize( $this->o_generatedCache->get( 'sitemap.index' ) );
		
		// cache is valid
		if( is_array( $a_cacheDatas ) && array_key_exists( 'expirationDate', $a_cacheDatas ) && $a_cacheDatas['expirationDate'] > gmdate('Y-m-d H:i:s') )
		{
			$this->isUpToDate			= true;
			$this->sitemapIndexContent	= $a_cacheDatas['sitemapIndexContent'];
		}
		else
		{
			$this->isUpToDate			= false;
			$this->sitemapIndexContent	= null;
		}
		// *************
	}

	/**
	 * Launch the execute of the generation process 
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 */
	public function execute()
	{
		// execute the generation process only if generated cached datas are not up-to-date
		if( !$this->isUpToDate() )
		{
			ob_start();
			
			// Get the list of all sitemap entries
			try
			{
				$dispatcher			= sfContext::getInstance()->getEventDispatcher();
				$dispatcher->notify( new sfEvent( $this, 'presta_sitemap.generate_urls' ) );
			}
			catch( Exception $e )
			{
				// log error message
				$this->a_errorMessages[]	= $e->getCode().': '.$e->getMessage();
			}
		
			// catch php undesired output
			$phpUndesiredOutput	= ob_get_clean();
			if( !empty( $phpUndesiredOutput ) )
			{
				$this->a_errorMessages[]	= $phpUndesiredOutput;
			}
			
			$this->saveInternalSectionCache();
			
			$this->generateAndSaveCacheFiles();
		}
	}
	
	
	/**
	 * Return the sitemap index's content. It contain the whole xml string or null if generation as failed somewhere
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @return String|null
	 */
	public function getCachedSitemapIndexContent()
	{
		return $this->sitemapIndexContent;
	}
	
	/**
	 * Return a sitemap content from cache
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @param String $mapName - The name of the map file
	 * @return String
	 */
	public function getCachedSitemapContent( $mapName )
	{
		return $this->o_generatedCache->get( $mapName, null );
	}
	
	/**
	 * Indicate where this section is up-to-date in cache or not
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @return Boolean
	 */
	protected function isUpToDate()
	{
		return $this->isUpToDate;
	}
	
	/**
	 * Save the internal datas in cache
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 */
	protected function saveInternalSectionCache()
	{
		$a_cacheExpirationDates	= array();
		
		// *************
		// *** Clean all internal cache datas
		// *************
		
		$this->o_internalCache->clean();
		// *************
		
		// *************
		// *** Store datas in cache
		// *************
		
		foreach( $this->a_o_sitemapSections as $o_sitemapSection )
		{
			// filter out empty urls
			$o_sitemapSection->deleteEmptyUrls();
			
			// notice the expiration date of thoses datas
			$cacheId	= $o_sitemapSection->getSectionId();
			$a_cacheExpirationDates[ $cacheId ]	= $o_sitemapSection->getExpirationDate();
			
			$a_toSave	= array();
			foreach( $o_sitemapSection->getUrls() as $o_sitemapUrl )
			{
				$a_toSave[]	= $o_sitemapUrl->toArray();
			}
			
			// store the file, unsing jsonencode( it take less memory than serialize)
			$this->o_internalCache->set( $cacheId , json_encode( $a_toSave ) ); 
		}
		// *************
		
		
		// *************
		// *** Store cache index file in cache
		// *************
		
		$this->o_internalCache->set( 'cacheIndex', serialize( $a_cacheExpirationDates ) );
		// *************
	}
	
	
	/**
	 * Respond to the 'presta_sitemap.new_sitemap_section' event called when a new prestaSection is instanciated
	 *
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param sfEvent $event
	 * @param prestaSitemapSection $sitemapSection
	 */
	public function addNewSitemapSectionEvent( sfEvent $event )
	{
		$this->a_o_sitemapSections[]	= $event->getSubject();
		return $this;
	}
	
	/**
	 * Return data loaded from the internal cache for the sectionStackTrace and the sectionName
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @param String $sectionId - sitemapSection unique Id
	 * @return Array or null if no valid datas have been founded
	 */
	public function getInternalCacheDatas( $sectionId )
	{
		static $a_loadedExpirationDates = null;
		$a_cacheDatas			= null;
		$sitemapUrlClassName	= sfConfig::get( 'app_prestaSitemapPlugin_sitemapUrlClassName' );
		
		// load cache index file
		if( is_null( $a_loadedExpirationDates ) )
		{
			$a_loadedExpirationDates	= unserialize( $this->o_internalCache->get( 'cacheIndex' ) );
		}
		
		// if cache is valid we'll load datas of this section
		if( is_array( $a_loadedExpirationDates ) && array_key_exists( $sectionId, $a_loadedExpirationDates ) && $a_loadedExpirationDates[ $sectionId ] > gmdate('Y-m-d H:i:s') )
		{
			$a_cacheDatas['expirationDate']		= $a_loadedExpirationDates[ $sectionId ];
			$a_cacheDatas['a_o_sitemapUrls']	= array();
			foreach( json_decode( $this->o_internalCache->get( $sectionId, json_encode( array() ) ), true ) as $jsonSitemapUrl )
			{
				$o_sitemapUrl	= new $sitemapUrlClassName();
				$o_sitemapUrl->fromArray( $jsonSitemapUrl );
				$a_cacheDatas['a_o_sitemapUrls'][]	= $o_sitemapUrl;
			}
		}
		
		return $a_cacheDatas;
	}
	
	
	/**
	 * Generate and save in cache xml sitemap files
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 21 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 21 juil. 2009 - Christophe Dolivet
	 */
	protected function generateAndSaveCacheFiles()
	{
		$a_o_sitemapUrlsBySections	= array();
		$a_mapNames					= array();
		$maxEntryCount				= sfConfig::get( 'app_prestaSitemapPlugin_maxEntryCountByFile' );
		
		// *************
		// *** list all the url to display and group them by section name
		// *************
		
		foreach( $this->a_o_sitemapSections as $o_sitemapSection )
		{
			if( !array_key_exists( $o_sitemapSection->getName(), $a_o_sitemapUrlsBySections ) )
			{
				$a_o_sitemapUrlsBySections[ $o_sitemapSection->getName() ]	= array();
			}
			
			// concat the 2 arrays
			$a_o_sitemapUrlsBySections[ $o_sitemapSection->getName() ]	+= $o_sitemapSection->getUrls();
		}
		// *************
		
		
		// *************
		// *** generate cache version of each xml sitemap files
		// *************
		
		$a_o_sitemapUrlsToReport	= array();
		$sfUser						= sfContext::getInstance()->getUser();
			 
		foreach( $a_o_sitemapUrlsBySections as $sectionName => $a_o_unlimitedSitemapUrls )
		{
			$groupNumber	= 0;	
			while( count( $a_o_unlimitedSitemapUrls ) > 0 )
			{
				$a_o_limitedSitemapUrls	= array_splice( $a_o_unlimitedSitemapUrls, 0, $maxEntryCount );
				$mapName				= $groupNumber > 0 ? $sectionName.'_'.( $groupNumber + 1 ) : $sectionName;
				
				// generate the file content
				$xmlFileContent		= get_partial( 'buildSitemap', array( 'a_o_sitemapUrls' => $a_o_limitedSitemapUrls ) );
				// store xml file in cache
				$this->o_generatedCache->set( $mapName, $xmlFileContent );	
				$a_mapNames[]		= $mapName;
				
				$groupNumber++;
				
				// some url may have not been renderedered (due to file size limit)
				$urlsToReportCounter	= $sfUser->getAttribute( 'urlsToReportCounter', 0, 'prestaSitemap' );
				
				if( $urlsToReportCounter > 0 )
				{
					// add thoses url to the begining of the array that is still to be rendered
					$a_o_unlimitedSitemapUrls	= array_slice( $a_o_limitedSitemapUrls, $urlsToReportCounter * -1 ) + $a_o_unlimitedSitemapUrls;
				}
			}
			
			// free memory
			unset( $a_o_sitemapUrlsBySections[ $sectionName ] );
		}
		
		$sfUser->getAttributeHolder()->removeNamespace( 'prestaSitemap' );
		// *************
		
		
		// *************
		// *** Generate the sitemap index file
		// *************
		
		$a_cacheParams	= sfConfig::get('app_prestaSitemapPlugin_mainCache');
		$expirationDate	= $a_cacheParams['enabled'] ? gmdate( 'Y-m-d H:i:s', time() + $a_cacheParams['lifetime'] ) : '0000-00-00 00:00:00';
		
		// generate the file content
		$this->sitemapIndexContent	= get_partial( 'buildSitemapIndex', array( 'a_mapNames' => $a_mapNames, 'a_errorMessages' => $this->a_errorMessages ) );
		
		// store xml file in cache
		$this->o_generatedCache->set( 'sitemap.index', serialize( array(	'expirationDate'		=> $expirationDate,
																			'sitemapIndexContent' 	=> $this->sitemapIndexContent ) ) );
		// *************
	}
	
	
	/**
	 * Check that there is at least a sitemap.index file in generated cache
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 7 août 2009 - Christophe Dolivet
	 * @version 1.0 - 7 août 2009 - Christophe Dolivet
	 * @return boolean
	 */
	public function isGeneratedCacheEmpty()
	{
		return $this->o_generatedCache->has( 'sitemap.index' ) ? false : true;
	}

	/**
	 * Return a string that represent the url in a format valid for directories
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 30 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 30 juil. 2009 - Christophe Dolivet
	 * @param String $url - The url to convert
	 * @return String
	 */
	protected function getExecutionStringIdentifierFromUrl()
	{
		$request		= sfContext::getInstance()->getRequest();
		// just use the host + relativeUrlRoot
		$url			= preg_replace( '@^http(?:s)?://(.*)$@', '$1', $request->getHost().$request->getRelativeUrlRoot() );
		
		// convert URI to simple string
		$string 		= htmlentities( $url, ENT_NOQUOTES, 'UTF-8' );
		$string 		= preg_replace( "/&([a-z])[a-z]+;/i", "$1", $string );
		preg_match_all( '/[a-zA-Z0-9\-_\.]+/', $string, $nt );
		$result			= strtolower( implode( '_', $nt[0] ) );
		
		return $result;
	}
}
