<?php

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



// *************
// *** Test prestaSitemapPLugin integration process
// *************

$browser = new sfTestBrowser();
$browser->test()->diag('prestaSitemapPlugin integration process');

// create a new test browser
$browser->
	get( 'sitemap.xml' )->
	isStatusCode(200)->
	isRequestParameter('module', 'prestaSitemap')->
	isRequestParameter('action', 'displaySitemapIndex')->
	responseContains('<sitemapindex')->
	responseContains('</sitemapindex>');
  
$dom = $browser->getResponseDom();
foreach( $dom->getElementsByTagName('loc') as $loc )
{
	$url	= $loc->firstChild->wholeText;
	$browser->test()->diag('Test '. $url);
	$browser->
  		get( $url )->
  		isStatusCode(200)->
		responseContains('<urlset')->
		responseContains('</urlset>');
		
	$dom2 = $browser->getResponseDom();
	$browser->test()->cmp_ok( count( $dom2->getElementsByTagName('url') ), '<', 50000, "There is less than 50000 entries" );
	
	
	ob_start();
	echo $browser->getResponse()->getContent();
	$length = ob_get_length();
	ob_end_clean();
	$browser->test()->cmp_ok( $length, '<', 10485760, "The file should have a size < 10.485.760" );
}
