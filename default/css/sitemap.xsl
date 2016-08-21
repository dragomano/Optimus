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
body {
    width: 60%;
	background: #ccc;
    margin: 40px auto;
    font-family: 'trebuchet MS', 'Lucida sans', Arial;
    font-size: 14px;
    color: #444;
}

h1,h3 {text-align: center}

table {
    *border-collapse: collapse;
	background: #fff;
    border-spacing: 0;
    width: 100%;  
    border: solid #ccc 1px;
    border-radius: 6px;
    box-shadow: 0 1px 1px #ccc; 	
}

tr:hover {
    background: #fbf8e9;
    -o-transition: all 0.1s ease-in-out;
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition: all 0.1s ease-in-out;
    -ms-transition: all 0.1s ease-in-out;
    transition: all 0.1s ease-in-out;     
}    
    
td, th {
    border-left: 1px solid #ccc;
    border-top: 1px solid #ccc;
    padding: 10px;
    text-align: left;    
}

th {
    background-color: #dce9f9;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ebf3fc), to(#dce9f9));
    background-image: -webkit-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:    -moz-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:     -ms-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:      -o-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:         linear-gradient(top, #ebf3fc, #dce9f9);
    box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;        
    border-top: none;
    text-shadow: 0 1px 0 rgba(255,255,255,.5); 
}

td:first-child, th:first-child {
    border-left: none;
}

th:first-child {
    border-radius: 6px 0 0 0;
}

th:last-child {
    border-radius: 0 6px 0 0;
}

tr:last-child td:first-child {
    border-radius: 0 0 0 6px;
}

tr:last-child td:last-child {
    border-radius: 0 0 6px 0;
}

tbody tr:nth-child(even) {
    background: #f5f5f5;
    box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;        
}  

span {
	display: block;
	margin: 1em;
	border-radius: 6px;
	background-color:#F1F1F1;
	padding:10px;
	text-align:center
}
	</style>
	<title>Sitemap<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - Mobile</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - Images</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">News</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - Video</xsl:if><xsl:if test="sm:sitemapindex"> - Index</xsl:if></title>
</head>
<body>
	<h1>Sitemap<xsl:if test="sm:urlset/sm:url/mobile:mobile"> - Mobile</xsl:if><xsl:if test="sm:urlset/sm:url/image:image"> - Images</xsl:if><xsl:if test="sm:urlset/sm:url/news:news">News</xsl:if><xsl:if test="sm:urlset/sm:url/video:video"> - Video</xsl:if><xsl:if test="sm:sitemapindex"> - Index</xsl:if></h1>
	<h3><xsl:choose><xsl:when test="sm:sitemapindex">Total files: <xsl:value-of select="count(sm:sitemapindex/sm:sitemap)"/></xsl:when><xsl:otherwise>Total URLs: <xsl:value-of select="count(sm:urlset/sm:url)"/></xsl:otherwise></xsl:choose></h3>
	<xsl:apply-templates/>
	<span>Powered by <a href="http://dragomano.ru/mods/optimus">Optimus</a></span>
</body>
</html>
</xsl:template>
<xsl:template match="sm:sitemapindex">
	<table>
		<tr>
			<th></th>
			<th>URL</th>
			<th>Last Modified</th>
		</tr>
		<xsl:for-each select="sm:sitemap">
		<tr> 
			<xsl:variable name="loc"><xsl:value-of select="sm:loc"/></xsl:variable>
			<xsl:variable name="pno"><xsl:value-of select="position()"/></xsl:variable>
			<td><xsl:value-of select="$pno"/></td>
			<td><a href="{$loc}"><xsl:value-of select="sm:loc"/></a></td>
			<xsl:apply-templates/> 
		</tr>
		</xsl:for-each>
	</table>
</xsl:template>
<xsl:template match="sm:urlset">
	<table>
		<tr>
			<th></th>
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
		<tr> 
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