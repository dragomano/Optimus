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
			thead {
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
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="sm:url">
					<xsl:variable name="loc"><xsl:value-of select="sm:loc"/></xsl:variable>
					<xsl:variable name="pno"><xsl:value-of select="position()"/></xsl:variable>
					<xsl:variable name="hasMedia" select="count(image:*) + 2 * count(video:video)"/>

					<tr class="windowbg">
						<td rowspan="{1 + $hasMedia}"><xsl:value-of select="$pno"/></td>
						<td><a href="{$loc}"><xsl:value-of select="sm:loc"/></a></td>
						<xsl:apply-templates select="sm:*"/>
					</tr>

					<xsl:if test="image:*">
						<tr class="windowbg">
							<td><strong>{direct_link}</strong></td>
							<td>
								<xsl:apply-templates select="image:*"/>
							</td>
							<td><strong>{thumbnail}</strong></td>
							<td>
								<xsl:if test="image:image/image:loc">
									<img src="{image:image/image:loc}" alt="" width="100"/>
								</xsl:if>
							</td>
						</tr>
					</xsl:if>

					<xsl:if test="video:video">
						<tr class="windowbg">
							<td>
								<xsl:if test="video:video/video:title">
									<strong><xsl:value-of select="video:video/video:title"/></strong>
								</xsl:if>
							</td>
							<td colspan="3">
								<xsl:if test="video:video/video:description">
									<xsl:value-of select="video:video/video:description"/>
								</xsl:if>
							</td>
						</tr>
						<tr class="windowbg">
							<td><strong>{direct_link}</strong></td>
							<td>
								<xsl:if test="video:video/video:content_loc">
									<a href="{video:video/video:content_loc}">
										<xsl:value-of select="video:video/video:content_loc"/>
									</a>
								</xsl:if>
							</td>
							<td><strong>{thumbnail}</strong></td>
							<td>
								<xsl:if test="video:video/video:thumbnail_loc">
									<img src="{video:video/video:thumbnail_loc}" alt="{video:video/video:title}" width="100"/>
								</xsl:if>
							</td>
						</tr>
					</xsl:if>
				</xsl:for-each>
			</tbody>
		</table>
	</div>
</xsl:template>
<xsl:template match="sm:loc|image:loc">
</xsl:template>
<xsl:template match="sm:lastmod|sm:changefreq|sm:priority">
	<td><xsl:apply-templates/></td>
</xsl:template>
<xsl:template match="image:image">
	<xsl:variable name="loc"><xsl:value-of select="image:loc"/></xsl:variable>
	<a href="{$loc}"><xsl:value-of select="image:loc"/></a>
	<xsl:apply-templates/>
</xsl:template>
</xsl:stylesheet>