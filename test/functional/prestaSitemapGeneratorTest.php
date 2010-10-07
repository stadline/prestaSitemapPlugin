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



include(dirname(__FILE__).'/../bootstrap/functional.php');

prestaSitemapTestUtils::createContextInstance();
prestaSitemapTestUtils::loadHelpers( array('Asset', 'Url' ) );
sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));

/**
 * Listener to 'controller.change_action' for sftestBrowser actions
 * 
 * @author  Christophe Dolivet
 * @since   1.0 - 31 juil. 2009 - Christophe Dolivet
 * @version 1.0 - 31 juil. 2009 - Christophe Dolivet
 * @param $sfEvent
 */
function _changeActionListener( $sfEvent )
{
	// if the module is prestaSitemap...
	if( $sfEvent['module'] == 'prestaSitemap' )
	{
		// cleanup all listener for 'presta_sitemap.generate_urls' except for '_generateUrlsListener'
		$dispatcher	= sfContext::getInstance()->getEventDispatcher();
		foreach( $dispatcher->getListeners('presta_sitemap.generate_urls') as $listener )
		{
			if( $listener != '_generateUrlsListener' )
			{
				$dispatcher->disconnect( 'presta_sitemap.generate_urls', $listener );
			}
		}
		// apply default config
		prestaSitemapTestUtils::applyDefaultConfig();
	}
}

function _generateUrlsListener( $sfEvent )
{
	// Define parameters
	$miscUrl			= 'http://www.foor.bar?a=1&b=2&a=???&c=Iñtërnâtiônàlizætiøn&amp;d=encoded&e=invalidXml<sdf>#toto';
	$longUrl			= $miscUrl.'thisisaveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryveryverylongurl';
	$o_date				= new DateTime();
	$priority			= 0.6;
	$changeFrequency	= prestaSitemapUrl::CHANGE_FREQUENCY_WEEKLY;
	
	// test misc section
	$o_sitemapSection	= new prestaSitemapSection( 'misc2', array( 'lifetime' => '60' ) );
	if( !$o_sitemapSection->isUpToDate() )
	{
		$o_sitemapSection->addUrl( new prestaSitemapUrl( $miscUrl, $o_date, $changeFrequency, $priority ) );
		// add an empty url
		$o_sitemapSection->addUrl( new prestaSitemapUrl() );
	}

	// test heavy loading (should be splited in multiple files due to file size limit)
	$o_sitemapSection	= new prestaSitemapSection( 'longTest');
	if( !$o_sitemapSection->isUpToDate() )
	{
		$o_url	= new prestaSitemapUrl( $longUrl, $o_date, $changeFrequency, $priority );
		for( $i = 0; $i< 49999; $i++ )
		{
			// create a lots of very long url
			$o_sitemapSection->addUrl( $o_url );
		}
	}

	// test heavy loading (should be splited in multiple files due to number limit)
	$o_sitemapSection	= new prestaSitemapSection( 'multipleTest');
	if( !$o_sitemapSection->isUpToDate() )
	{
		$o_url	= new prestaSitemapUrl( $miscUrl, $o_date, $changeFrequency, $priority );
		for( $i = 0; $i< 100001; $i++ )
		{
			// create a lots of very long url
			$o_sitemapSection->addUrl( $o_url );
		}
	}
}

// *************
// *** Test prestaSitemapGenrator process
// *************


$browser = new sfTestFunctional(new sfBrowser());


$browser->test()->diag('prestaSitemapGenerator process');

$browser->addListener('controller.change_action', '_changeActionListener' );
$browser->addListener('presta_sitemap.generate_urls', '_generateUrlsListener' );


// create a new test browser
$browser->get( 'sitemap.xml' )->

	with('request')->begin()->
		isParameter('module', 'prestaSitemap')->
		isParameter('action', 'displaySitemapIndex')->
	end()->

	with('response')->begin()->
		isStatusCode(200)->
		checkElement('sitemapindex')->
	end();

	
  
$dom = $browser->getResponseDom();
foreach( $dom->getElementsByTagName('loc') as $loc )
{
	$url	= $loc->firstChild->wholeText;
	//$browser->test()->diag('Test '. $url);
	
	$browser->get( $url )->
		with('response')->begin()->
	  		isStatusCode(200)->
			checkElement('urlset')->
		end();
		
	$dom2 = $browser->getResponseDom();
	$browser->test()->cmp_ok( count( $dom2->getElementsByTagName('url') ), '<', 50000, "There is less than 50000 entries" );
	
	
	ob_start();
	echo $browser->getResponse()->getContent();
	$length = ob_get_length();
	ob_end_clean();
	$browser->test()->cmp_ok( $length, '<', 10485760, "The file should have a size < 10.485.760" );
}

// non existing seciton will return a 404
$browser->get( 'sitemap.nonExisting.xml' )->
	with('response')->begin()->
		isStatusCode(404)->
	end();


sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));
// existing section should regenerate cache datas if cache is empty
$browser->get( 'sitemap.misc2.xml' )->
	with('response')->begin()->
		isStatusCode(200)->
	end();

// non existing section will return a 404
sfToolkit::clearDirectory(sfConfig::get('sf_cache_dir'));
$browser->get( 'sitemap.nonExisting.xml' )->
	with('response')->begin()->
		isStatusCode(404)->
	end();
	