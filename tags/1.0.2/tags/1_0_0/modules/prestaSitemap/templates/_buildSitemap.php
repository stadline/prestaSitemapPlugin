<?php $counter	= count( $a_o_sitemapUrls ) ; ?>
<?php $maxSize	= sfConfig::get( 'app_prestaSitemapPlugin_maxFileSize' ) - 10000; ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n" ?>
<urlset 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach( $a_o_sitemapUrls as $o_sitemapUrl ): ?>
	<?php 
		$xml	= $o_sitemapUrl->toXML();
		// take into account the fact that there is a max file size allowed for sitemap
		// Note: strlen won't return correct byte value but the error won't be more than some bytes  (this is why we take a 10000 octet margin to the max length
		if( ob_get_length() + strlen( $xml ) >= $maxSize )
		{
			break;
		}
		echo $xml;
		$counter--;
	?>
<?php endforeach; ?>
</urlset>
<?php $sf_user->setAttribute( 'urlsToReportCounter', $counter, 'prestaSitemap' ); ?>