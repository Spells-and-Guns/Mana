This is the default skin for the various Scratch Wikis.

## Installation
1. Download the contents of this repository and save them into $wgScriptPath/skins/ScratchWikiSkin2 (a new directory)
2. Add the following line to LocalSettings.php:
```
wfLoadSkin( 'ScratchWikiSkin2' );
```
3. If desired, set $wgDefaultSkin as follows:
```
$wgDefaultSkin = 'scratchwikiskin2';
```

## Customizing per wiki
The same skin is meant to serve every Spells and Guns wiki. The defaults are the
Spells and Guns look; each wiki overrides what it needs in `LocalSettings.php`,
after `wfLoadSkin`. Nothing in the skin folder has to change.

```php
// Colors: any variable from the :root block in resources/css/main.css.
// The older ones (font-color, link-*, rarity-*, …) are "R, G, B[, A]" triplets;
// page-color, nav-color, heading-color and box-color are normal CSS colors.
$wgSWS2Colors = [
	'link-color' => '255, 247, 0, 0.9',
	'link-hover-color' => '255, 247, 0, 1',
	'nav-color' => '#2a1f3f',
];

// Images (any URL; put wiki-specific files under images/ or resources/).
$wgSWS2Background = "$wgResourceBasePath/resources/background.jpg";
$wgSWS2HeaderLogo = "$wgResourceBasePath/resources/logo.png"; // false hides the big logo
$wgLogos['wordmark'] = [ 'src' => "$wgResourceBasePath/resources/sng-logo.png", 'width' => 70, 'height' => 70 ]; // navbar logo
$wgSWS2Cursor = "$wgResourceBasePath/resources/custom-cursor.webp";        // optional
$wgSWS2CursorLink = "$wgResourceBasePath/resources/custom-cursorlink.webp"; // optional

// Links. Labels are message keys (translated, editable at MediaWiki:<key>) or plain text;
// targets are URLs, /paths or page names. Setting a list replaces the default one.
$wgSWS2NavLogoLink = 'https://www.spellsandguns.com/';
$wgSWS2EditGuide = 'Wiki_Editing'; // '' hides it
$wgSWS2FooterBanner = [ 'sng-footer-discord-cta' => 'https://discord.gg/wqRBSytTmP' ]; // [] hides it
$wgSWS2FooterLinks = [
	'sng-footer-wiki-home' => '/',
	'Discord' => 'https://discord.spellsandguns.com',
	'sng-footer-terms-of-use' => 'Terms_of_Use',
	'Privacy policy' => 'Privacy_policy',
];
```

The top navigation bar is built from the wiki's `MediaWiki:Sidebar` page: a section
with one link becomes a plain link, a section with several becomes a dropdown.
Header logo size: `$wgSWS2Colors['header-logo-height'] = '200px';`

All the text (footer labels, "Help the {{SITENAME}}!", …) comes from interface
messages, so it can also be changed on the wiki itself under `MediaWiki:<message key>`.
Wiki-specific styling that isn't a color or an image, such as extra template classes,
belongs in that wiki's `MediaWiki:Common.css`.
