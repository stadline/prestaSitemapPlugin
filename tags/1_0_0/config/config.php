<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// If routing rule have to be use. If no configuration rule given, rules will be set
if( in_array( 'prestaSitemap', sfConfig::get('sf_enabled_modules') ) && sfConfig::get( 'app_prestaSitemapPlugin_routing', true ) )
{
	$this->dispatcher->connect( 'routing.load_configuration', array( 'prestaSitemapRouting', 'listenToRoutingLoadConfigurationEvent' ) );
}


// *************
// *** Define default parameters
// *************

// be sure that main values for cache parameters are defined
sfConfig::set( 'app_prestaSitemapPlugin_mainCache', array_merge( array( 'enabled' => 'on', 'lifetime' => 3600 ), sfConfig::get( 'app_prestaSitemapPlugin_mainCache', array() ) ) );
sfConfig::set( 'app_prestaSitemapPlugin_sectionCache', array_merge( array( 'enabled' => 'on', 'lifetime' => 86400 ), sfConfig::get( 'app_prestaSitemapPlugin_sectionCache', array() ) ) );

// define the maximum number of entries for a sitemap file
if( !sfConfig::has( 'app_prestaSitemapPlugin_maxEntryCountByFile' ) )
{
	sfConfig::set( 'app_prestaSitemapPlugin_maxEntryCountByFile', 49999 );
}

// define the maximum number of entries for a sitemap file
if( !sfConfig::has( 'app_prestaSitemapPlugin_maxFileSize' ) )
{
	sfConfig::set( 'app_prestaSitemapPlugin_maxFileSize', 10485760 );
}

// define the root cache directory
if( !sfConfig::has( 'app_prestaSitemapPlugin_rootCacheDir' ) )
{
	sfConfig::set( 'app_prestaSitemapPlugin_rootCacheDir', sfConfig::get('sf_cache_dir').'/prestaSitemapPlugin' );
}

// define the classes to use
if( !sfConfig::has( 'app_prestaSitemapPlugin_sitemapGeneratorClassName' ) )
{
	sfConfig::set( 'app_prestaSitemapPlugin_sitemapGeneratorClassName', 'prestaSitemapGenerator' );
}
if( !sfConfig::has( 'app_prestaSitemapPlugin_sitemapUrlClassName' ) )
{
	sfConfig::set( 'app_prestaSitemapPlugin_sitemapUrlClassName', 'prestaSitemapUrl' );
}



// *************