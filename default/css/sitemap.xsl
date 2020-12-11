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
	<style type="text/css">
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}
		html {
			padding: 5px;
		}
		body {
			background: #e9eef2;
			font: 83.33%/150% "Segoe UI", "Helvetica Neue", "Nimbus Sans L", Arial, "Liberation Sans", sans-serif;
			color: #4d4d4d;
			border-radius: 6px;
			padding: 1em;
		}
		h1, h3 {
			text-align: center;
			margin: .6em;
		}
		table.table_grid {
			margin: 0;
			width: 100%;
			border-spacing: 0;
			border-collapse: collapse;
			border: 1px solid;
			border-top: none;
		}
		.table_head > th {
			padding: 5px 8px;
			text-align: left;
			font-weight: bold;
			font-size: 1em;
			border-top: 2px solid;
			border-bottom: 2px solid;
		}
		.roundframe {
			margin: 10px 0 0 0;
			padding: 12px 16px;
			background: #eee;
			border: 1px solid #c5c5c5;
			border-radius: 7px;
			box-shadow: 0 -2px 2px rgba(0, 0, 0, 0.1);
			overflow: auto;
		}
		.windowbg {
			background: #f0f4f7;
			margin: 12px 0 0 0;
			border: 1px solid #ddd;
			border-radius: 6px;
			box-shadow: none;
			overflow: auto;
		}
		.windowbg:nth-of-type(even) {
			background: #FAFAFA;
		}
		.windowbg:nth-of-type(odd) {
			background: #FEFEFE;
		}
		tr.windowbg:hover {
			background: #e2eef8;
		}
		th, td {
			padding: 4px 8px;
			word-break: break-all;
		}
		.footer {
			border-top: 3px solid #3d6e32;
			background: #222;
			box-shadow: 0 -1px 0 #686868,0 1px 0 #0e0e0e inset;
			color: #bbb;
			margin: 1em auto;
			padding: 10px;
			text-align: center;
		}
		@media (max-width: 720px) {
			table {
				margin-top: 1em !important;
			}
			tr {
				display: block;
				margin: 1px !important;
			}
			th, td:first-child {
				display: none;
			}
			td {
				display: flex;
				justify-content: center;
			}
		}
	</style>
	<title>Sitemap<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - Mobile</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - Images</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">News</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - Video</xsl:if><xsl:if test="sm:sitemapindex"> - Index</xsl:if></title>
</head>
<body>
	<h1>Sitemap<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - Mobile</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - Images</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">News</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - Video</xsl:if><xsl:if test="sm:sitemapindex"> - Index</xsl:if></h1>
	<h3><xsl:choose><xsl:when test="sm:sitemapindex">Total files: <xsl:value-of select="count(sm:sitemapindex/sm:sitemap)"/></xsl:when><xsl:otherwise>Total URLs: <xsl:value-of select="count(sm:urlset/sm:url)"/></xsl:otherwise></xsl:choose></h3>
	<xsl:apply-templates/>
	<div class="footer">Powered by Optimus for ElkArte</div>
</body>
</html>
</xsl:template>
<xsl:template match="sm:sitemapindex">
	<div class="roundframe">
	<table class="table_grid">
		<tr class="table_head">
			<th>*</th>
			<th>URL</th>
		</tr>
		<xsl:for-each select="sm:sitemap">
		<tr class="windowbg">
			<xsl:variable name="loc"><xsl:value-of select="sm:loc"/></xsl:variable>
			<xsl:variable name="pno"><xsl:value-of select="position()"/></xsl:variable>
			<td><xsl:value-of select="$pno"/></td>
			<td><a href="{$loc}"><xsl:value-of select="sm:loc"/></a></td>
			<xsl:apply-templates/>
		</tr>
		</xsl:for-each>
	</table>
	</div>
</xsl:template>
<xsl:template match="sm:urlset">
	<div class="roundframe">
	<table class="table_grid">
		<tr class="table_head">
			<th>*</th>
			<th>URL</th>
			<xsl:if test="sm:url/sm:lastmod"><th>Last Modified</th></xsl:if>
			<xsl:if test="sm:url/sm:changefreq"><th>Frequency</th></xsl:if>
			<xsl:if test="sm:url/sm:priority"><th>Priority</th></xsl:if>
			<xsl:if test="sm:url/image:image/image:loc"><th>Direct link</th></xsl:if>
			<xsl:if test="sm:url/image:image/image:caption"><th>Caption</th></xsl:if>
			<xsl:if test="sm:url/video:video/video:content_loc"><th>Direct link</th></xsl:if>
			<xsl:if test="sm:url/video:video/video:thumbnail_loc"><th>Thumbnail</th></xsl:if>
			<xsl:if test="sm:url/video:video/video:title"><th>Caption</th></xsl:if>
		</tr>
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