<?php
	// fix data that could have been changed due to output escaping strategy
	$sitemapContent	= $sf_data->getRaw( 'sitemapContent' );
	
	// display the cache datas
	if( !empty( $sitemapContent ) )
	{
		// display raw datas (avoid to fall under output escaping strategy)
		echo $sitemapContent;
	}
	// display at least an empty sitemap
	else
	{
		include_partial( 'buildSitemap', array( 'a_o_sitemapUrls' => array() ) ); 
	}