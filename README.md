# Optimus

[![SMF 2.1](https://img.shields.io/badge/SMF-2.1-ed6033.svg?style=flat)](https://github.com/SimpleMachines/SMF2.1)
[![SMF 3.0 Alpha](https://img.shields.io/badge/SMF-3.0_Alpha-ed2533.svg?style=flat)](https://github.com/SimpleMachines/SMF/tree/release-3.0)
![License](https://img.shields.io/github/license/dragomano/optimus)
![Hooks only: Yes](https://img.shields.io/badge/Hooks%20only-YES-blue)
[![Coverage Status](https://coveralls.io/repos/github/dragomano/Optimus/badge.svg?branch=main)](https://coveralls.io/github/dragomano/Optimus?branch=main)
![PHP](https://img.shields.io/badge/PHP-^8.2-blue.svg?style=flat)
[![Crowdin](https://badges.crowdin.net/smf-optimus/localized.svg)](https://crowdin.com/project/smf-optimus)
[![Docs](https://img.shields.io/badge/Docs-Site-orange.svg)](https://dragomano.github.io/Optimus/)

- **Tested on:** PHP 8.3.36 / MariaDB 11.8.5
- **Languages:** English, Russian, Dutch, Turkish, Spanish, German, Italian, Polish, Portuguese, Romanian

### Reviews

- [На русском](https://dragomano.ru/mods/optimus)

### Features

- Managing of title and description for the main forum page.
- Managing of title, description, and keywords meta-tags for topic pages.
- Description meta-tag for boards' and topics' pages.
- Keywords meta-tag for topics. Users can fill them when creating/changing topics.
- Support of Open Graph meta-tags and Twitter Cards.
- Ability to specify the verification codes when adding sites to Google, Yandex, and Bing (Webmaster Tools).
- Adding custom meta-tags.
- Adding any JS/CSS code without template editing.
- Changing the robots.txt and .htaccess from the Settings page.
- Generator for the robots.txt file.
- Simple Sitemap XML.
- Managing of favicon.
- Statistics of search queries on the forum.
- Ability to create simple redirects.
- Support of addons.

### Sitemap example

<details>
<summary>Click to show</summary>

```json
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="https://example.com/index.php?action=sitemap_xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">

    <!-- Simple link -->
    <url>
        <loc>https://example.com/page1</loc>
        <lastmod>2024-01-29</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Image link -->
    <url>
        <loc>https://example.com/page2</loc>
        <lastmod>2024-01-28</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        <image:image>
            <image:loc>https://user-images.githubusercontent.com/229402/72751885-9368fb80-3be2-11ea-9497-51484f9d5646.jpg</image:loc>
        </image:image>
    </url>

    <!-- Video link -->
    <url>
        <loc>https://example.com/page3</loc>
        <lastmod>2024-01-27</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
        <video:video>
            <video:content_loc>https://example.com/videos/video1.mp4</video:content_loc>
            <video:thumbnail_loc>https://user-images.githubusercontent.com/229402/72751885-9368fb80-3be2-11ea-9497-51484f9d5646.jpg</video:thumbnail_loc>
            <video:title>Sample Video 1</video:title>
            <video:description>Description</video:description>
        </video:video>
    </url>

    <!-- Image & Video link -->
    <url>
        <loc>https://example.com/page4</loc>
        <lastmod>2024-01-26</lastmod>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
        <image:image>
            <image:loc>https://user-images.githubusercontent.com/229402/72751885-9368fb80-3be2-11ea-9497-51484f9d5646.jpg</image:loc>
        </image:image>
        <video:video>
            <video:content_loc>https://example.com/videos/video2.mp4</video:content_loc>
            <video:thumbnail_loc>https://user-images.githubusercontent.com/229402/72751885-9368fb80-3be2-11ea-9497-51484f9d5646.jpg</video:thumbnail_loc>
            <video:title>Sample Video 2</video:title>
            <video:description>Description</video:description>
        </video:video>
    </url>

</urlset>
```

</details>
