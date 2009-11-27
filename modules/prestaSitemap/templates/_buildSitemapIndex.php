<?php
	// fix data that could have been changed due to output escaping strategy
	$a_errorMessages	= $sf_data->getRaw( 'a_errorMessages' );
	$a_mapNames			= $sf_data->getRaw( 'a_mapNames' );
	// compute only once the current date
	$lastModDate		= date('c');
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<sitemapindex
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
<?php if( isset( $a_errorMessages ) && count( $a_errorMessages ) > 0 ): ?>
	xmlns:error="http://www.foo.bar/error"
<?php endif; ?>
>
<?php foreach( $a_mapNames as $mapName ): ?>
	<sitemap>
		<loc><?php echo url_for( '@prestaSitemap_map?mapName='.$mapName, 'absolute=true' ) ?></loc>
		<lastmod><?php echo $lastModDate ?></lastmod>
	</sitemap>
<?php endforeach; ?>
<?php if( isset( $a_errorMessages ) && count( $a_errorMessages ) > 0 ): ?>
	<error:messages>
<?php foreach( $a_errorMessages as $errorMessage ): ?>
		<error:message><![CDATA[<?php echo $errorMessage ?>]]></error:message>
<?php endforeach; ?>
	</error:messages>
<?php endif; ?>
</sitemapindex>