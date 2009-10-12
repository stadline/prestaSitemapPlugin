<?php
	// display the cache datas
	if( !empty( $sitemapIndexContent ) )
	{
		echo $sitemapIndexContent;
	}
	// diplsay at least an empty sitemap
	else
	{
		include_partial( 'buildSitemapIndex', array( 'a_mapNames' => array() ) ); 
	}