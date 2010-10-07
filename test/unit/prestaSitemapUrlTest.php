<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */



require_once(dirname(__FILE__).'/../bootstrap/unit.php');

prestaSitemapTestUtils::createContextInstance();
prestaSitemapTestUtils::loadHelpers( array('Asset', 'Url' ) );

$t = new lime_test(18, new lime_output_color());

// Define parameters
$miscUrl			= 'http://www.foor.bar?a=1&b=2&a=而在尊&c=Iñtërnâtiônàlizætiøn&amp;d=encoded&e=invalidXml<sdf>#toto';
$o_date				= new DateTime();
$priority			= 0.6;
$changeFrequency	= prestaSitemapUrl::CHANGE_FREQUENCY_WEEKLY;

// *************
// *** Test prestaSitemapUrl class
// *************

$t->diag('prestaSitemapUrl');

$t->diag('1 - setLocation()');
$o_prestaSitemapUrl	= new prestaSitemapUrl();

$o_prestaSitemapUrl->setLocation( $miscUrl );
$t->isnt( @simplexml_load_string( $o_prestaSitemapUrl->toXML() ), false, 'produce valid xml with non valid characters in location');

$longUrl	= str_pad( 'http://', 2047, '_' );
$o_prestaSitemapUrl->setLocation( $longUrl );
$t->isnt( $o_prestaSitemapUrl->getLocation(), null, 'Accept url < 2048 characters');
$longUrl	.= '_';
$o_prestaSitemapUrl->setLocation( $longUrl );
$t->is( $o_prestaSitemapUrl->getLocation(), null, 'Refuse url >= 2048 characters');

$t->diag('2 - Change frequency');

$o_prestaSitemapUrl->setChangeFrequency( prestaSitemapUrl::CHANGE_FREQUENCY_DAILY );
$t->isnt( $o_prestaSitemapUrl->getChangeFrequency(), null, "Accept valid value" );

$o_prestaSitemapUrl->setChangeFrequency( 'bouh' );
$t->is( $o_prestaSitemapUrl->getChangeFrequency(), null, "Refuse invalid value" );



$t->diag('3 - Priority');
$o_prestaSitemapUrl->setPriority( 0.5 );
$t->isnt( $o_prestaSitemapUrl->getPriority(), null, "Accept valid priority" );

$o_prestaSitemapUrl->setPriority( 'aaa' );
$t->is( $o_prestaSitemapUrl->getPriority(), null, "Doesn't accept invalid priority" );
$o_prestaSitemapUrl->setPriority( -0.01 );
$t->is( $o_prestaSitemapUrl->getPriority(), null, "Doesn't accept invalid priority" );
$o_prestaSitemapUrl->setPriority( 1.01 );
$t->is( $o_prestaSitemapUrl->getPriority(), null, "Doesn't accept invalid priority" );
$o_prestaSitemapUrl->setPriority( 0.58 );
$t->is( $o_prestaSitemapUrl->getPriority(), 0.6, "Round frequency to 1 decimal after digit" );

$t->diag('4 - Modification date');
$o_prestaSitemapUrl->setLastModificationDate( $o_date );
$t->is( $o_prestaSitemapUrl->getLastModificationDate(), $o_date->format('c'), "Produce ISO 8601 date string (valid W3C Datetime format)" );

set_error_handler( array( 'prestaSitemapTestUtils', 'fakeErrorHandler' ) );
$o_prestaSitemapUrl->setLastModificationDate( date('Y-m-d H:i:s' ) );
$t->is( $o_prestaSitemapUrl->getLastModificationDate(), null, "Require DateTime object in entry" );
restore_error_handler();

$t->diag('5 - Misc');
$o_prestaSitemapUrl1	= new prestaSitemapUrl( $miscUrl, $o_date, $changeFrequency, $priority );
$o_prestaSitemapUrl2	= new prestaSitemapUrl();
$o_prestaSitemapUrl2->setLocation( $miscUrl )->setLastModificationDate( $o_date )->setChangeFrequency( $changeFrequency )->setPriority( $priority );
$t->cmp_ok( $o_prestaSitemapUrl1->toXML(), '==', $o_prestaSitemapUrl2->toXml(), 'Defining parameters to constructor or through setter produce same xml');

$o_prestaSitemapUrl1	= new prestaSitemapUrl();
$o_prestaSitemapUrl1->fromArray( $o_prestaSitemapUrl2->toArray() );
$t->cmp_ok( $o_prestaSitemapUrl1->toXML(), '==', $o_prestaSitemapUrl2->toXml(), 'fromArray() and toArray() methods allow to exports/import datas without anychange');

$t->diag('6 - Images');

$o_prestaSitemapUrl1		= new prestaSitemapUrl();
$o_prestaSitemapUrlImage1	= new prestaSitemapUrlImage( 'img_test.png', 'alt_1', 'geo_location_1', 'title_1', 'license_1' );
$o_prestaSitemapUrlImage2	= new prestaSitemapUrlImage( null, 'alt_2', 'geo_location_2', 'title_2', 'license_2' );
$o_prestaSitemapUrlImage3	= new prestaSitemapUrlImage( 'img_test.png', null, null, null, null );

$o_prestaSitemapUrl1->addImage($o_prestaSitemapUrlImage1);
$o_prestaSitemapUrl1->addImage($o_prestaSitemapUrlImage2);
$o_prestaSitemapUrl1->addImage($o_prestaSitemapUrlImage3);

$t->is( count($o_prestaSitemapUrl1->getImages()), 3, "Image successfull associated" );

$test = true;
foreach( $o_prestaSitemapUrl1->getImages() as $o_prestaSitemapUrlImage )
{
	if( ! $o_prestaSitemapUrlImage instanceof prestaSitemapUrlImage )
	{
		$test = false;
	}
}
$t->ok( $test, "Image successfull returned" );

$o_prestaSitemapUrl1->deleteEmptyUrls();

$t->is( count($o_prestaSitemapUrl1->getImages()), 2, "Image with empty url successfull deleted" );


for( $i = 0; $i <= sfConfig::get('app_prestaSitemapPlugin_maxImagePerPage') + 1  ; $i++ )
{
	$o_prestaSitemapUrl1->addImage($o_prestaSitemapUrlImage1);
}

$t->cmp_ok( count($o_prestaSitemapUrl1->getImages()), '>', sfConfig::get('app_prestaSitemapPlugin_maxImagePerPage'), 'Limit of ' . sfConfig::get('app_prestaSitemapPlugin_maxImagePerPage') . ' image per page successfully respected');