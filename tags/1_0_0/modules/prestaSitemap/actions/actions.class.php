<?php

/*
 * This file is part of the prestaSitemaplugin package.
 * (c) Chriistophe Dolivet <cdolivet@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


/**
 * prestaSitemap main module's actions
 * 
 * @author  Christophe Dolivet
 * @version 1.0 - 4 août 2009 - Christophe Dolivet
 */
class prestaSitemapActions extends sfActions
{
	/**
	 * Instanciate a new sitemap generator
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 22 juil. 2009 - Christophe Dolivet
	 * @return Object
	 */
	protected function getNewSitemapGenerator()
	{
		// gérer le cache de cette action
		$generatorClass		= sfConfig::get( 'app_prestaSitemapPlugin_sitemapGeneratorClassName' );
		
		// instanciate the sitemap generator
		return new $generatorClass();
	}
	
	
	/**
	 * Main action used for generating the sitemap
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param sfWebRequest $request
	 */
	public function executeDisplaySitemapIndex( sfWebRequest $request )
	{
		$o_sitemapGenerator	= $this->getNewSitemapGenerator();
		
		// execute the sitemap generation process
		$o_sitemapGenerator->execute();
		
		$this->sitemapIndexContent	= $o_sitemapGenerator->getCachedSitemapIndexContent();
		
		return sfView::SUCCESS;
	}
	
	
	/**
	 * Action used for displaying a cached sitemap
	 * 
	 * @author  Christophe Dolivet
	 * @since   1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @version 1.0 - 15 juil. 2009 - Christophe Dolivet
	 * @param sfWebRequest $request
	 */
	public function executeDisplaySitemap( sfWebRequest $request )
	{
		$o_sitemapGenerator			= $this->getNewSitemapGenerator();
		
		// execute the sitemap generation process if sitemap generated cache is empty
		if( $o_sitemapGenerator->isGeneratedCacheEmpty() )
		{
			$o_sitemapGenerator->execute();
		}
		
		// get the sitemap content
		$this->sitemapContent		= $o_sitemapGenerator->getCachedSitemapContent( $request->getParameter('mapName') );
		
		// generate a 404 message if sitemap content is null
		$this->forward404If( is_null( $this->sitemapContent ) );
		
		return sfView::SUCCESS;
	}
}