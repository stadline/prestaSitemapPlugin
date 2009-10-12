<?php
	// display the cache datas
	if( !empty( $sitemapContent ) )
	{
		echo $sitemapContent;
	}
	// diplsay at least an empty sitemap
	else
	{
		include_partial( 'buildSitemap', array( 'a_o_sitemapUrls' => array() ) ); 
	}