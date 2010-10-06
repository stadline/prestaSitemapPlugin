<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



/**
 * Utility class for unit and functionnal test of prestaSitemapPlugin
 * @author  Christophe Dolivet
 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
 */
class prestaSitemapTestUtils
{
	public static function createContextInstance()
	{
		try
		{
			sfContext::getInstance();
		}
		catch( Exception $e )
		{
			$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true );
			sfContext::createInstance($configuration);
		}
	} 
	
	
	public static function loadHelpers( $datas )
	{
		// load helper as we'll use partial rendering and probably url_for methods
		sfContext::getInstance()->getConfiguration()->loadHelpers( array( 'Asset', 'Partial', 'Url' ) );
	}
	
	public static function urlXmlContentToXML( $xml )
	{
		return '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'. $xml .'</urlset>';
	}
	
	public static function fakeErrorHandler()
	{
		
	}
	
	/**
	 * Apply default plugin config
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 31 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
	 */
	public static function applyDefaultConfig()
	{
		sfConfig::set( 'app_prestaSitemapPlugin_mainCache', array( 'enabled' => 'off', 'lifetime' => 3600 ) );
		sfConfig::set( 'app_prestaSitemapPlugin_sectionCache', array( 'enabled' => 'off', 'lifetime' => 7000 ) );
		sfConfig::set( 'app_prestaSitemapPlugin_maxEntryCountByFile', 49999 );
		sfConfig::set( 'app_prestaSitemapPlugin_maxFileSize', 10485760 );
		sfConfig::set( 'app_prestaSitemapPlugin_rootCacheDir', sfConfig::get('sf_cache_dir').'/prestaSitemapPlugin' );
		sfConfig::set( 'app_prestaSitemapPlugin_sitemapGeneratorClassName', 'prestaSitemapGenerator' );
		sfConfig::set( 'app_prestaSitemapPlugin_sitemapUrlClassName', 'prestaSitemapUrl' );
	}
	
}