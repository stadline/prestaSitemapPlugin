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

// If routing rule have to be use. If no configuration rule given, rules will be set
if( in_array( 'prestaSitemap', sfConfig::get('sf_enabled_modules') ) && sfConfig::get( 'app_prestaSitemapPlugin_routing', true ) )
{
	$this->dispatcher->connect( 'routing.load_configuration', array( 'prestaSitemapRouting', 'listenToRoutingLoadConfigurationEvent' ) );
}
