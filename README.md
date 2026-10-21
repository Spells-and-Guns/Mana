# Mana

The skin of the Spells and Guns wikis. It started as a fork of
[ScratchWikiSkin2](https://github.com/InternationalScratchWiki/ScratchWikiSkin2),
the Scratch Wikis' skin; skin.json keeps its original authors and CREDITS its attributions.

## Installation
1. Save the contents of this repository into `skins/Mana` (a new directory).
2. Add the following line to LocalSettings.php:
```
wfLoadSkin( 'Mana' );
```
3. If desired, make it the default skin:
```
$wgDefaultSkin = 'mana';
```

## Customizing per wiki
The same skin is meant to serve every Spells and Guns wiki. The defaults are the
Spells and Guns look; each wiki overrides what it needs in `LocalSettings.php`,
after `wfLoadSkin`. Nothing in the skin folder has to change.

```php
// Colors: any variable from the :root block in resources/css/main.css.
// The older ones (font-color, link-*, rarity-*, …) are "R, G, B[, A]" triplets;
// page-color, nav-color, heading-color and box-color are normal CSS colors.
$wgManaColors = [
	'link-color' => '255, 247, 0, 0.9',
	'link-hover-color' => '255, 247, 0, 1',
	'nav-color' => '#2a1f3f',
];

// Images (any URL; put wiki-specific files under images/ or resources/).
$wgManaBackground = "$wgResourceBasePath/resources/background.jpg";
$wgManaHeaderLogo = "$wgResourceBasePath/resources/logo.png"; // false hides the big logo
$wgLogos['wordmark'] = [ 'src' => "$wgResourceBasePath/resources/sng-logo.png", 'width' => 70, 'height' => 70 ]; // navbar logo
$wgManaCursor = "$wgResourceBasePath/resources/custom-cursor.webp";        // optional
$wgManaCursorLink = "$wgResourceBasePath/resources/custom-cursorlink.webp"; // optional

// Links. Labels are message keys (translated, editable at MediaWiki:<key>) or plain text;
// targets are URLs, /paths or page names. Setting a list replaces the default one.
$wgManaNavLogoLink = 'https://www.spellsandguns.com/';
$wgManaEditGuide = 'Wiki_Editing'; // '' hides it
$wgManaFooterBanner = [ 'sng-footer-discord-cta' => 'https://discord.gg/wqRBSytTmP' ]; // [] hides it
$wgManaFooterLinks = [
	'sng-footer-wiki-home' => '/',
	'Discord' => 'https://discord.spellsandguns.com',
	'sng-footer-terms-of-use' => 'Terms_of_Use',
	'Privacy policy' => 'Privacy_policy',
];
```

The top navigation bar is built from the wiki's `MediaWiki:Sidebar` page: a section
with one link becomes a plain link, a section with several becomes a dropdown.
Header logo size: `$wgManaColors['header-logo-height'] = '200px';`

All the text (footer labels, "Help the {{SITENAME}}!", …) comes from interface
messages, so it can also be changed on the wiki itself under `MediaWiki:<message key>`.
Wiki-specific styling that isn't a color or an image, such as extra template classes,
belongs in that wiki's `MediaWiki:Common.css`.
