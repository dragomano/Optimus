<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet	version="1.0"
	xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"
	xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
	xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
	xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
	xmlns:fo="http://www.w3.org/1999/XSL/Format"
	xmlns="http://www.w3.org/1999/xhtml">
<xsl:output method="xml" indent="yes" encoding="UTF-8"/>
<xsl:template match="/">
<html>
<head>
	<link rel="stylesheet" href="{link}"/>
	<style type="text/css">
		html {
			height: 100%;
		}
		body {
			display: flex;
			flex-direction: column;
		}
		h1, h2 {
			text-align: center;
			margin: .6em;
			flex: unset !important;
		}
		.header {
			flex: 0 0 auto;
		}
		.content {
			flex: 1 0 auto;
		}
		.roundframe {
			margin: 0 auto;
			width: 90%;
		}
		table.table_grid {
			border-collapse: collapse;
			text-align: center;
			empty-cells: show;
		}
		#footer {
			text-align: center;
			flex: 0 0 auto;
		}
		@media (max-width: 720px) {
			table {
				margin-top: 1em !important;
			}
			tr {
				display: block;
				margin: 1px !important;
			}
			thead, td:first-child {
				display: none;
			}
			td {
				display: flex;
				justify-content: center;
			}
		}
	</style>
	<title>{sitemap}<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - {mobile}</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - {images}</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">{news}</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - {video}</xsl:if><xsl:if test="sm:sitemapindex"> - {index}</xsl:if></title>
</head>
<body>
	<div class="header">
		<h1 class="forumtitle">{sitemap}<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - {mobile}</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - {images}</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">{news}</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - {video}</xsl:if><xsl:if test="sm:sitemapindex"> - {index}</xsl:if></h1>
		<h2 class="titlebg"><xsl:choose><xsl:when test="sm:sitemapindex">{total_files}: <xsl:value-of select="count(sm:sitemapindex/sm:sitemap)"/></xsl:when><xsl:otherwise>{total_urls}: <xsl:value-of select="count(sm:urlset/sm:url)"/></xsl:otherwise></xsl:choose></h2>
	</div>
	<div class="content">
		<xsl:apply-templates/>
	</div>
	<div id="footer">
		<div class="inner_wrap"><p>Powered by {optimus}</p></div>
	</div>
</body>
</html>
</xsl:template>
<xsl:template match="sm:sitemapindex">
	<div class="roundframe">
		<table class="table_grid word_break">
			<thead>
				<tr class="title_bar">
					<th>*</th>
					<th>{url}</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="sm:sitemap">
				<tr class="windowbg">
					<xsl:variable name="loc"><xsl:value-of select="sm:loc"/></xsl:variable>
					<xsl:variable name="pno"><xsl:value-of select="position()"/></xsl:variable>
					<td><xsl:value-of select="$pno"/></td>
					<td><a href="{$loc}"><xsl:value-of select="sm:loc"/></a></td>
					<xsl:apply-templates/>
				</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</div>
</xsl:template>
<xsl:template match="sm:urlset">
	<div class="roundframe">
		<table class="table_grid word_break">
			<thead>
				<tr class="title_bar">
					<th>*</th>
					<th>{url}</th>
					<xsl:if test="sm:url/sm:lastmod"><th>{last_modified}</th></xsl:if>
					<xsl:if test="sm:url/sm:changefreq"><th>{frequency}</th></xsl:if>
					<xsl:if test="sm:url/sm:priority"><th>{priority}</th></xsl:if>
					<xsl:if test="sm:url/image:image/image:loc"><th>{direct_link}</th></xsl:if>
					<xsl:if test="sm:url/image:image/image:caption"><th>{caption}</th></xsl:if>
					<xsl:if test="sm:url/video:video/video:content_loc"><th>{direct_link}</th></xsl:if>
					<xsl:if test="sm:url/video:video/video:thumbnail_loc"><th>{thumbnail}</th></xsl:if>
					<xsl:if test="sm:url/video:video/video:title"><th>{caption}</th></xsl:if>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="sm:url">
				<tr class="windowbg">
					<xsl:variable name="loc"><xsl:value-of select="sm:loc"/></xsl:variable>
					<xsl:variable name="pno"><xsl:value-of select="position()"/></xsl:variable>
					<td><xsl:value-of select="$pno"/></td>
					<td><a href="{$loc}"><xsl:value-of select="sm:loc"/></a></td>
					<xsl:apply-templates select="sm:*"/>
					<xsl:apply-templates select="image:*"/>
					<xsl:apply-templates select="video:*"/>
				</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</div>
</xsl:template>
<xsl:template match="sm:loc|image:loc|video:content_loc|video:*">
</xsl:template>
<xsl:template match="sm:lastmod|sm:changefreq|sm:priority|image:caption|video:title">
	<td><xsl:apply-templates/></td>
</xsl:template>
<xsl:template match="image:image">
	<xsl:variable name="loc"><xsl:value-of select="image:loc"/></xsl:variable>
	<td class="url2"><a href="{$loc}"><xsl:value-of select="image:loc"/></a></td>
	<xsl:apply-templates/>
</xsl:template>
<xsl:template match="video:video">
	<xsl:variable name="loc"><xsl:choose><xsl:when test="video:player_loc != ''"><xsl:value-of select="video:player_loc"/></xsl:when><xsl:otherwise><xsl:value-of select="video:content_loc"/></xsl:otherwise></xsl:choose></xsl:variable>
	<xsl:variable name="thumb"><xsl:value-of select="video:thumbnail_loc"/></xsl:variable>
	<td class="url2"><a href="{$loc}"><xsl:choose><xsl:when test="video:player_loc != ''"><xsl:value-of select="video:player_loc"/></xsl:when><xsl:otherwise><xsl:value-of select="video:content_loc"/></xsl:otherwise></xsl:choose></a></td>
	<td><xsl:if test="video:thumbnail_loc != ''"><img src="{$thumb}" alt=""/></xsl:if></td>
	<xsl:apply-templates/>
</xsl:template>
</xsl:stylesheet>