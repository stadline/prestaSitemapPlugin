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
 * Class containing method called for adding routing rules
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class prestaSitemapRouting
{
	/**
	 * Listens to the routing.load_configuration event.
	 *
	 * @param sfEvent An sfEvent instance
	 */
	static public function listenToRoutingLoadConfigurationEvent( sfEvent $event )
	{
		$r = $event->getSubject();
		
		// be sure to get default config for thoses rules
		$a_routesOptions	= array(
			'segment_separators'	=> array( '/', '.' ),
			'variable_prefixes'		=> array( ':' ),
			'variable_regex'		=> '[\w\d_]+'
		);
		
		// preprend our routes
		$r->prependRoute(	'prestaSitemap_index',
							new sfRoute(	'/sitemap.xml',
											array(	'module' => 'prestaSitemap',
													'action' => 'displaySitemapIndex' ),
											array(),
											$a_routesOptions ) );
		$r->prependRoute(	'prestaSitemap_map',
							new sfRoute( 	'/sitemap.:mapName.xml', 
											array(	'module'		=> 'prestaSitemap',
													'action' 		=> 'displaySitemap' ),
											array(	'requirement'	=> array( 'mapName' => '[a-zA-Z0-9]+' ) ),
											$a_routesOptions ) );
	}
}