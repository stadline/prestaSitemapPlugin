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



require_once(dirname(__FILE__).'/../bootstrap/unit.php');

prestaSitemapTestUtils::createContextInstance();
prestaSitemapTestUtils::loadHelpers( array('Asset', 'Url' ) );

$t = new lime_test(10, new lime_output_color());

// Define parameters
$img_name		= 'img_test.png';
$caption 		= 'alt';
$geo_location	= 'geo_location';
$title 			= 'title';
$license 		= 'license';
$miscUrl 		= 'http://www.foor.bar?a=1&b=2&a=而在尊&c=Iñtërnâtiônàlizætiøn&amp;d=encoded&e=invalidXml<sdf>#toto';

// *************
// *** Test prestaSitemapUrlImage class
// *************

$t->diag('prestaSitemapUrlImage');

$t->diag('1.1 - setLocation()');
$o_prestaSitemapUrlImage	= new prestaSitemapUrlImage();

$o_prestaSitemapUrlImage->setLocation( $img_name );
$t->is( $o_prestaSitemapUrlImage->getLocation(), image_path( $img_name, true ), 'Image path in standard setLocation() method successfully generated');

$t->diag('1.2 - setLocationUrlFor()');
$o_prestaSitemapUrlImage->setLocationUrlFor( $miscUrl );
$t->isnt( @simplexml_load_string( $o_prestaSitemapUrlImage->toXML() ), false, 'produce valid xml with non valid characters in location');

$t->diag('1.3 - setLocation() with different url length ');
$longUrl	= str_pad( 'http://', 2047, '_' );
$o_prestaSitemapUrlImage->setLocation( $longUrl );
$t->isnt( $o_prestaSitemapUrlImage->getLocation(), null, 'Accept url < 2048 characters');
$longUrl	.= '_';
$o_prestaSitemapUrlImage->setLocation( $longUrl );
$t->is( $o_prestaSitemapUrlImage->getLocation(), null, 'Refuse url >= 2048 characters');


$t->diag('2 setter and getter');
$o_prestaSitemapUrlImage->setCaption($caption)->setGeoLocation($geo_location)->setTitle($title)->setLicense($license);

$t->diag('2.1 - getCaption()');
$t->is( $o_prestaSitemapUrlImage->getCaption(), $caption, 'Caption setter and getter correctly work');

$t->diag('2.2 - getGeoLocation()');
$t->is( $o_prestaSitemapUrlImage->getGeoLocation(), $geo_location, 'Geo location setter and getter correctly work');

$t->diag('2.3 - getTitle()');
$t->is( $o_prestaSitemapUrlImage->getTitle(), $title, 'Title setter and getter correctly work');

$t->diag('2.4 - getLicense()');
$t->is( $o_prestaSitemapUrlImage->getLicense(), $license, 'License setter and getter correctly work');


$t->diag('3 - Misc');
$o_prestaSitemapUrlImage1	= new prestaSitemapUrlImage( $miscUrl, 'alt', 'geo_location', 'title', 'license' );
$o_prestaSitemapUrlImage2	= new prestaSitemapUrlImage();
$o_prestaSitemapUrlImage2->setLocation( $miscUrl )->setCaption( 'alt' )->setGeoLocation( 'geo_location' )->setTitle( 'title' )->setLicense( 'license' );
$t->cmp_ok( $o_prestaSitemapUrlImage1->toXML(), '==', $o_prestaSitemapUrlImage2->toXml(), 'Defining parameters to constructor or through setter produce same xml');

$o_prestaSitemapUrlImage1	= new prestaSitemapUrlImage();
$o_prestaSitemapUrlImage1->fromArray( $o_prestaSitemapUrlImage2->toArray() );
$t->cmp_ok( $o_prestaSitemapUrlImage1->toXML(), '==', $o_prestaSitemapUrlImage2->toXml(), 'fromArray() and toArray() methods allow to exports/import datas without anychange');