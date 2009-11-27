<?php
	// fix data that could have been changed due to output escaping strategy
	$sitemapIndexContent	= $sf_data->getRaw( 'sitemapIndexContent' );
	
	// display the cache datas
	if( !empty( $sitemapIndexContent ) )
	{
		// display raw datas (avoid to fall under output escaping strategy)
		echo $sitemapIndexContent;
	}
	// diplsay at least an empty sitemap
	else
	{
		include_partial( 'buildSitemapIndex', array( 'a_mapNames' => array() ) ); 
	}