<?php
/**
 * BaseTemplate class for Mana
 *
 * @file
 * @ingroup Skins
 */

use MediaWiki\MediaWikiServices;

require_once __DIR__ . '/consts.php';

class ManaTemplate extends BaseTemplate {
	public function execute() {
		global $wgRightsPage, $wgRightsUrl, $wgRightsIcon, $wgRightsText, $wgLang, $wgManaJoinBox;
		$user = RequestContext::getMain()->getUser();
		$config = $this->getSkin()->getConfig();
		$pre139 = version_compare( MW_VERSION, '1.39', '<' );

		if ( $pre139 ) {
			$this->html('headelement');
		}
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		$colorPref = $userOptionsLookup->getOption( $user, HEADER_COLOR_PREF );
		$darkPref = $userOptionsLookup->getOption( $user, DARK_THEME_PREF );
		?>
<?=$this->themeStyle( $config )?>
<?php
$logos = MediaWiki\ResourceLoader\SkinModule::getAvailableLogos( $config );
$wordmark = $logos['wordmark']['src'] ?? $this->get('stylepath') . '/' . $this->getSkin()->stylename . '/resources/sng-logo.png';
$wordmarkW = $logos['wordmark']['width'] ?? 70;
$wordmarkH = $logos['wordmark']['height'] ?? 70;
// Sidebar boxes (excluding TOOLBOX) drive the project-specific top-nav links.
$sidebarWithoutToolbox = $this->getSidebar();
unset( $sidebarWithoutToolbox['TOOLBOX'] );
?>
<div id="navigation" role="banner">
	<div class="inner">
		<ul>
			<li class="link"><a class="jump-to" href="#logo" tabindex="0"><span><?=wfMessage('mana-jump-to-sidebar')->escaped()?></span></a></li>
			<li class="link"><a class="jump-to" href="#firstHeading" tabindex="1"><span><?=wfMessage('mana-jump-to-content')->escaped()?></span></a></li>
			<li class="link menu">
				<a class="dropdown-toggle" aria-label="<?=wfMessage( 'sng-menu' )->escaped()?>"></a>
				<ul class="dropdown">
<?php
// Mobile menu: every MediaWiki:Sidebar link in one list. Single-link boxes show the box header.
foreach ( $sidebarWithoutToolbox as $box ) {
	if ( empty( $box['content'] ) || !is_array( $box['content'] ) ) continue;
	$items = count( $box['content'] ) === 1
		? [ [ 'text' => $box['header'], 'href' => reset( $box['content'] )['href'] ?? '#' ] ]
		: $box['content'];
	foreach ( $items as $name => $item ) {
		$href = $item['href'] ?? '#';
		$external = (bool) preg_match( '#^https?://#', $href );
?>
					<li><a href="<?= htmlspecialchars( $href ) ?>"<?= $external ? ' target="_blank" rel="noopener"' : '' ?>><span><?= htmlspecialchars( $item['text'] ?? $name ) ?></span></a></li>
<?php
	}
}
?>
				</ul>
			</li>
			<li class="logo"><a aria-label="<?=wfMessage( 'sng-nav-logo-label' )->escaped()?>" href="<?=htmlspecialchars( $config->get( 'ManaNavLogoLink' ) ?: $this->data['nav_urls']['mainpage']['href'] )?>">
				<img alt="<?=wfMessage('sitetitle')->inContentLanguage()->escaped()?>" src="<?=htmlspecialchars($wordmark) ?>" height="<?= $wordmarkH ?>" width="<?= $wordmarkW ?>">
			</a></li>
<?php
// Project-specific top-nav: render one item per MediaWiki:Sidebar box (excluding TOOLBOX).
// Single-link boxes render as a flat link; multi-link boxes render as a dropdown.
foreach ( $sidebarWithoutToolbox as $sectionKey => $box ) {
	if ( empty( $box['content'] ) || !is_array( $box['content'] ) ) continue;
	if ( count( $box['content'] ) === 1 ) {
		$item = reset( $box['content'] );
		$href = $item['href'] ?? '#';
		$external = (bool) preg_match( '#^https?://#', $href );
?>
			<li class="link about">
				<a href="<?= htmlspecialchars( $href ) ?>"<?= $external ? ' target="_blank" rel="noopener"' : '' ?>><span><?= htmlspecialchars( $box['header'] ) ?></span></a>
			</li>
<?php
	} else {
?>
			<li class="link create">
				<a class="dropdown-toggle"><span><?= htmlspecialchars( $box['header'] ) ?></span></a>
				<ul class="dropdown">
<?php foreach ( $box['content'] as $name => $item ) { ?>
					<li><a href="<?= htmlspecialchars( $item['href'] ?? '#' ) ?>"><span><?= htmlspecialchars( $item['text'] ?? $name ) ?></span></a></li>
<?php } ?>
				</ul>
			</li>
<?php
	}
}
?>
			<li class="search">
				<form class="form" action="<?php $this->text( 'wgScript' ) ?>" role="search" aria-label="<?=wfMessage( 'mana-search' )->inLanguage( $wgLang )->escaped()?>">
					<button class="button btn-search" tabindex="-1"></button>
					<div class="form-group row no-label">
						<div class="col-sm-9">
							<input
								type="text" class="input" id="searchInput"
								accesskey="<?=wfMessage( 'accesskey-search' )->inLanguage( $wgLang )->escaped() ?>"
								title="<?=wfMessage(
									'mana-search-title',
									wfMessage( 'accesskey-search' )->inLanguage( $wgLang )->text()
								)->inLanguage( $wgLang )->escaped()?>"
								name="search" autocomplete="off"
								placeholder="<?=wfMessage( 'mana-search' )->inLanguage( $wgLang )->escaped() ?>"
							/>
							<input type="hidden" value="<?=SpecialPage::getTitleFor( 'Search' )->getPrefixedDBkey()?>" name="title" />
						</div>
					</div>
				</form>
			</li>
<?php
// Start processing dropdown here so we know its output before it saves to the page, and we can use it earlier.
$personalUrls = [];
$echoUnread = 0;
foreach ($this->data['personal_urls'] as $key => $tab) {
	// additional class for non-link items
	if (!array_key_exists('href', $tab)) {
		if (!array_key_exists('class', $tab)) $tab['class'] = [];
		if (is_array($tab['class'])) array_push($tab['class'], 'no-link');
		else $tab['class'] .= ' no-link';
	}
	// treat "You have a new Talk page message" as 1 notification...
	if (array_key_exists('class', $tab) && is_array($tab['class']) && in_array('mw-echo-alert', $tab['class'])) {
		// ...if not already counted
		if ($echoUnread === 0) $echoUnread = 1;
		continue; // don't include the item in personal URLs
	}
	// skip other Echo notices
	if (strpos($key, 'notifications') !== false) {
		// get number of Echo notifications
		if ($key === 'notifications-alert') $echoUnread = $tab['data']['counter-num'] ?? $echoUnread;
		continue; // don't include the item in personal URLs
	}
	array_push($personalUrls, $this->getSkin()->makeListItem($key, $tab));
}
// ONLY output this if Echo extension is turned on and user is logged in. Otherwise don't put it there.
if (ExtensionRegistry::getInstance()->isLoaded("Echo") && $user->isRegistered()) { ?>
			<li class="link right messages">
				<a href="<?=SpecialPage::getTitleFor( 'Notifications' )->getLinkURL()?>">
					<span class="<?=$echoUnread > 0 ? 'message-count show' : 'message-count'?>"><?=$echoUnread?></span>
				</a>
			</li>
<?php } ?>
			<li class="link right content-actions">
				<a class="dropdown-toggle" href="?action=edit"><div></div></a>
				<ul class="dropdown">
<?php foreach ($this->data['content_actions'] as $key => $tab) { ?>
					<?=$this->getSkin()->makeListItem($key, $tab)?>
<?php } ?>
<?php if ( $config->get( 'ManaEditGuide' ) ) { ?>
					<li><a href="<?= htmlspecialchars( $this->linkHref( $config->get( 'ManaEditGuide' ) ) ) ?>"><span><?= wfMessage( 'sng-wikieditguide' )->inLanguage( $wgLang )->escaped() ?></span></a></li>
<?php } ?>
				</ul>
			</li>
			<li class="link right account-nav">
				<a class="dropdown-toggle" href="<?php if (!$user->isRegistered()) { ?><?=SpecialPage::getTitleFor( 'UserLogin' )->getLinkURL() ?><?php } else { ?><?=$user->getUserPage()->getLinkURL()?><?php } ?>">
					<span class="profile-name"><?php if (!$user->isRegistered()) { ?><?=wfMessage( 'mana-notloggedin' )->inLanguage( $wgLang )->escaped()?><?php } else { ?><?=htmlspecialchars($user->getName())?><?php } ?></span>
				</a>
				<ul class="dropdown">
				<?=implode($personalUrls)?>
				</ul>
			</li>
		</ul>
	</div>
</div>
<div id="not-nav">
<?php
$headerLogo = $config->get( 'ManaHeaderLogo' ) ?? $this->get( 'stylepath' ) . '/' . $this->getSkin()->stylename . '/resources/logo.png';
if ( $headerLogo ) { ?>
<div class="logoheader">
	<div class="logopart">
		<a href="<?= htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" title="<?= wfMessage( 'mainpage' )->inLanguage( $wgLang )->escaped() ?>">
			<img src="<?= htmlspecialchars( $headerLogo ) ?>" alt="<?= wfMessage( 'sitetitle' )->inContentLanguage()->escaped() ?>">
		</a>
	</div>
</div>
<?php } ?>
<div id="view">
	<div class="splash">
		<div class="inner mod-splash">
			<div class="left">
				<div class="wikilogo_space"><a id="logo" class="mw-wiki-logo" href="<?=htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>" title="<?=wfMessage( 'mainpage' )->inLanguage( $wgLang )->escaped()?>"></a></div>
<?php foreach ($this->getSidebar() as $box) { ?>
				<div class="box" role="navigation" aria-label="<?=htmlspecialchars($box['header'])?>">
					<div class="box-header">
						<h4><?=htmlspecialchars($box['header'])?></h4>
					</div>
					<div class="box-content">
<?php if (is_array($box['content'])) { ?>
						<ul>
<?php foreach ($box['content'] as $name => $item) { ?>
							<?=$this->getSkin()->makeListItem($name, $item)?>

<?php } ?>

						</ul>
<?php } else { ?>
						<?=$box['content']?>
<?php } ?>
					</div>
				</div>
<?php }
if (!$user->isRegistered() && $wgManaJoinBox) { ?>
				<div class="box" role="complementary" aria-label="<?=wfMessage( 'mana-helpthewiki' )->inLanguage( $wgLang )->escaped()?>">
					<div class="box-header">
						<h4><?=wfMessage( 'mana-helpthewiki' )->inLanguage( $wgLang )->escaped()?></h4>
					</div>
					<div class="box-content">
						<p><?=wfMessage( 'mana-madeforscratchers')->inLanguage( $wgLang )->parse()?></p>
						<p><a href="<?=Title::newFromText(wfMessage( 'mana-becomeacontributor-page' )->inContentLanguage()->text())->getLinkURL()?>"><?=wfMessage( 'mana-learnaboutjoining' )->inLanguage( $wgLang )->escaped()?></a></p>
						<p><a href="<?=Title::newFromText(wfMessage( 'portal-url' )->inContentLanguage()->text())->getLinkURL()?>"><?=wfMessage( 'mana-seeportal' )->inLanguage( $wgLang )->escaped()?></a></p>
					</div>
				</div>
<?php } ?>
			</div>
			<div class="right">
				<?php if ($this->data['newtalk']) { ?><div class="box"><div class="box-header"><h4><?php $this->html('newtalk') ?></h4></div></div><?php } ?>
				<?php if ($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice'); ?></div><?php } ?>
				<div class="box">
					<div class="box-header">
						<?=$this->getIndicators()?>
						<h1 id="firstHeading" class="firstHeading"><?php $this->html('title')?></h1>
					</div>
					<div class="box-content" id="content" role="main">
<p id="siteSub"><?=wfMessage( 'tagline' )->inLanguage( $wgLang )->escaped()?></p>
<?php if ($this->data['subtitle']) { ?><p id="contentSub"><?php $this->html('subtitle') ?></p><?php } ?>
<?php if ($this->data['undelete']) { ?><p><?php $this->html('undelete') ?></p><?php } ?>
<?php $this->html('bodytext') ?>
<?php $this->html('dataAfterContent'); ?>
<?php if ($this->data['catlinks']) {
	$this->html( 'catlinks' );
}
$url = wfMessage('mana-discuss-wiki')->inLanguage( $wgLang )->escaped();
$text = wfMessage('mana-discuss-wiki-text')->inLanguage( $wgLang )->escaped();
$link = "<a href=\"$url\" target=\"_blank\">$text</a>";
$line = wfMessage('mana-dark-theme-feedback')->rawParams( $link )->inLanguage( $wgLang )->escaped();
?>
					<div class="feet" style="margin: 0<?=$darkPref ? '' : '; display: none'?>"><?=$line?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="footer" role="contentinfo">
	<div class="inner">
		<ul class="feet">
<?php foreach ( $this->getFooterLinks()['info'] ?? [] as $key ) { ?>
			<li><?php $this->html( $key ) ?></li>
<?php } ?>
			<?php if (!empty( $wgRightsIcon )) { ?>
			<br/>
			<a href="<?=empty( $wgRightsPage ) ? $wgRightsUrl : Title::newFromText( $wgRightsPage )->getLinkURL()?>">
				<img style="position: absolute; bottom: 0; right: 0;" alt="<?=$wgRightsText?>" src="<?=$wgRightsIcon?>">
			</a>
			<?php } ?>
		</ul>
<?php if ( $config->get( 'ManaFooterBanner' ) ) { ?>
		<div class="lists" style="font-weight:bold;margin-bottom:10px">
			<span><?= $this->footerLinks( $config->get( 'ManaFooterBanner' ) ) ?></span>
		</div>
<?php } ?>
		<div class="lists">
			<span><?= $this->footerLinks( $config->get( 'ManaFooterLinks' ) ) ?></span>
		</div>
<?php
// Page tools (what links here, permanent link, …) from the sidebar's TOOLBOX.
$tools = [];
foreach ( $this->getSidebar()['TOOLBOX']['content'] ?? [] as $key => $item ) {
	if ( isset( $item['href'] ) ) {
		$tools[] = $this->getSkin()->makeLink( $key, $item );
	}
}
if ( $tools ) { ?>
		<div class="lists tools">
			<span><?= implode( ' | ', $tools ) ?></span>
		</div>
<?php } ?>
	</div>
</div><?php if (date('m j') == '04 1') { ?><div style="background-color:white;color:black">;</div><?php } ?>
</div>
<script>
/*
Mana script
*/

//get an element from a query selector, with functions to write, add, show, hide, addclass, delclass
function mod(el) {
	if (!el) return el;
	el.addclass = function(c) {this.classList.add(c);};
	el.delclass = function(c) {this.classList.remove(c);};
	el.hasclass = function(c) {return this.classList.contains(c);};
	return el;
}
var body = mod(document.body);
/* if (
	window.matchMedia
	&& window.matchMedia('(prefers-color-scheme: dark)').matches
	&& !body.hasclass('dark-theme')
) {
	body.addclass('dark-theme');
	document.querySelector('div.feet').style.display = '';
} */
(function () {
	let selected = document.querySelectorAll('#navigation a.dropdown-toggle');
	for (var i = 0; i < selected.length; i++) {
		let btn = selected[i];
		let dropdown = btn.nextElementSibling;
		mod(btn);
		mod(dropdown);
		btn.removeAttribute('href');
		btn.onclick = function(){
			if (!dropdown.classList.contains('open')) {
				btn.addclass('open');
				dropdown.addclass('open');
			} else {
				btn.delclass('open');
				dropdown.delclass('open');
			}
		};
		btn.parentElement.onmouseout = function(e) {
			if (!e) e = window.event;
			if (!e.toElement) e.toElement = e.relatedTarget;
			if (!e.toElement) return;
			if (
				!btn.parentElement.contains(e.toElement)
				&& !document.querySelector('.suggestions')?.contains(e.toElement)
			) {
				if (btn.hasclass('open')) {
					if (e.toElement.matches('#navigation .link:not([class~=messages]), #navigation .link>a.dropdown-toggle, #navigation .link>a.dropdown-toggle *')) {
						e.toElement.click();
					}
				}
				btn.delclass('open');
				dropdown.delclass('open');
			}
		};
	};
})();
var searchExpanded = false;
document.querySelector('#searchInput').onfocus = function () {
	let selected = document.querySelectorAll('#navigation .link');
	for (var i = 0; i < selected.length; i++) {
		let link = selected[i];
		if (!link.classList.contains('right')) {
			link.style.display = 'none';
		}
	}
	searchExpanded = true;
};
window.addEventListener('click', function (e) {
	if (!searchExpanded) return;
	if (document.querySelector('#navigation .search').contains(e.target)) return;
	let selected = document.querySelectorAll('#navigation .link');
	for (var i = 0; i < selected.length; i++) {
		let link = selected[i];
		if (!link.classList.contains('right')) {
			link.style.display = '';
		}
	}
	searchExpanded = false;
});

var sidebarShown = false;
var sidebarToggle = document.querySelector('#navigation .sidebar-toggle')

function toggleSidebar(e) {
	e.stopPropagation();

	if (getComputedStyle(sidebarToggle).display === 'none') return;

	const left = document.querySelector('#view .inner .left');
	const notNav = document.querySelector('#not-nav');

	if (!sidebarShown) {
		// show sidebar and enable non-nav close listener
		left.style.left = '0';
		notNav.addEventListener('click', toggleSidebar);
	} else {
		// ignore clicks in the sidebar itself
		if (left.contains(e.target)) return;
		// hide sidebar and disable non-nav close listener
		e.preventDefault();
		left.style.left = null;
		notNav.removeEventListener('click', toggleSidebar);
	}
	sidebarShown = !sidebarShown;
}

if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);

</script>

<?php
		if ( $pre139 ) {
			$this->printTrail();
		}
	}

	/**
	 * Inline :root overrides from $wgManaColors, $wgManaBackground and $wgManaCursor*.
	 * Defaults live in main.css; only what a wiki sets ends up here.
	 */
	private function themeStyle( Config $config ): string {
		$vars = $config->get( 'ManaColors' );
		if ( $config->get( 'ManaBackground' ) ) {
			$vars['background-image'] = 'url("' . $config->get( 'ManaBackground' ) . '")';
		}
		if ( $config->get( 'ManaCursor' ) ) {
			$vars['cursor'] = 'url("' . $config->get( 'ManaCursor' ) . '") 4 4, auto';
		}
		if ( $config->get( 'ManaCursorLink' ) ) {
			$vars['cursor-link'] = 'url("' . $config->get( 'ManaCursorLink' ) . '") 4 4, pointer';
		}
		$css = '';
		foreach ( $vars as $name => $value ) {
			$name = preg_replace( '/[^A-Za-z0-9-]/', '', $name );
			$value = str_replace( [ ';', '{', '}', '<' ], '', $value );
			$css .= "--$name: $value; ";
		}
		return $css === '' ? '' : "<style>:root { $css}</style>";
	}

	/**
	 * Render a [ label => target ] map as " | "-separated links. A label that is a
	 * message key is translated, anything else is shown as-is.
	 */
	private function footerLinks( array $links ): string {
		$html = [];
		foreach ( $links as $label => $target ) {
			$msg = wfMessage( $label );
			$text = $msg->exists() ? $msg->escaped() : htmlspecialchars( $label );
			$html[] = '<a href="' . htmlspecialchars( $this->linkHref( $target ) ) . '">' . $text . '</a>';
		}
		return implode( ' | ', $html );
	}

	/**
	 * URLs and absolute paths pass through; anything else is treated as a page name.
	 */
	private function linkHref( string $target ): string {
		if ( preg_match( '#^(https?:)?//|^/#', $target ) ) {
			return $target;
		}
		$title = Title::newFromText( $target );
		return $title ? $title->getLinkURL() : $target;
	}
}
