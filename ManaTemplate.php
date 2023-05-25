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
		global $wgLogo, $wgRightsPage, $wgRightsUrl, $wgRightsIcon, $wgRightsText, $wgLang, $wgSWS2JoinBox;
		$user = RequestContext::getMain()->getUser();
		$pre139 = version_compare( MW_VERSION, '1.39', '<' );

		if ( $pre139 ) {
			$this->html('headelement');
		}
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		// $colorPref = $userOptionsLookup->getOption( $user, HEADER_COLOR_PREF );
		$darkPref = $userOptionsLookup->getOption( $user, DARK_THEME_PREF );
		?>

<link rel="stylesheet" src="https://cdn.spellsandguns.com/<?= $wikiName ?>">
<div id="navigation" role="banner">
	<div class="inner">
		<ul>
			<li class="link"><a class="jump-to" href="#logo" tabindex="0"><span><?= wfMessage('sng-jump-to-sidebar')->escaped() ?></span></a></li>
			<li class="link"><a class="jump-to" href="#firstHeading" tabindex="1"><span><?= wfMessage('sng-jump-to-content')->escaped() ?></span></a></li>
			<li class="logo"><a aria-label="Spells&Guns" href="https://www.spellsandguns.com/">
					<img alt="<?= wfMessage('sitetitle')->inContentLanguage()->escaped() ?>" src="https://cdn.spellsandguns.com/sng_resources/logo.png" height="70" width="70">
				</a></li>
				
			<li class="link menu">
				<a class="dropdown-toggle" style="font-size:1.8rem;text-align:center;">&equiv;</a>
				<ul class="dropdown">
			
			<?php $sidebarWithoutToolbox = $this->getSidebar(); 
				unset($sidebarWithoutToolbox['TOOLBOX']);?>
			<?php foreach ($sidebarWithoutToolbox as $box) { ?>

					<?php if (count($box['content'])==1) { 
							$item = $box['content'][0];
							if (str_contains($item['href'], 'https://') or str_contains($item['href'], 'http://')) {?>
							<li><a href="<?= $item['href'] ?>" target="_blank"><span><?= $box['header'] ?></span></a></li>
							<?php }
							else {?>
							<li><a href="<?= $item['href'] ?>"><span><?= $box['header'] ?></span></a></li>
							<?php }?>
					<?php }
					else {?>
							<?php if (is_array($box['content'])) { ?>
									<?php foreach ($box['content'] as $name => $item) { ?>
									<li><a href="<?= $item['href'] ?>"><span><?= $item['text'] ?></span></a></li>
									<?php  } ?>
							<?php }?>
					<?php } ?>
			<?php } ?>
				</ul>
			</li>
			
			<?php foreach ($sidebarWithoutToolbox as $box) { ?>
				<?php if (count($box['content'])==1) { ?>
					<li class="link normal">
							<?php $item = $box['content'][0];
							if (str_contains($item['href'], 'https://') or str_contains($item['href'], 'http://')) {?>
							<a href="<?= $item['href'] ?>" target="_blank"><span><?= $box['header'] ?></span></a>
							<?php }
							else {?>
							<a href="<?= $item['href'] ?>"><span><?= $box['header'] ?></span></a>
							<?php }?>
					</li>
				<?php }
				else {?>
					<li class="link toggle">
						<a class="dropdown-toggle"><span><?= $box['header'] ?></span></a>
						<ul class="dropdown">
							
							<?php if (is_array($box['content'])) { ?>
									<?php foreach ($box['content'] as $name => $item) { ?>
									
									<li><a href="<?= $item['href'] ?>"><span><?=  $item['text'] ?></span></a></li>
									
									<?php  } ?>
							<?php } ?>
						</ul>
					</li>
				<?php } ?>
			<?php } ?>
			

			<li class="search">
				<form class="form" action="<?php $this->text('wgScript') ?>" role="search" aria-label="Search the Wiki">
					<button class="button btn-search" tabindex="-1"></button>
					<div class="form-group row no-label">
						<div class="col-sm-9">
							<input type="text" class="input" id="searchInput" accesskey="<?= wfMessage('accesskey-search')->inLanguage($wgLang)->escaped() ?>"  name="search" autocomplete="off" />
							<input type="hidden" value="Special:Search" name="title" />
						</div>
					</div>
				</form>
			</li>
			<li class="link right content-actions">
				<a class="dropdown-toggle" href="?action=edit">
					<div></div>
				</a>
				<ul class="dropdown">
					<?php foreach ($this->data['content_actions'] as $key => $tab) { ?>
						<?= $this->getSkin()->makeListItem($key, $tab) ?>

					<?php } ?>
					<li><a href="/Wiki_Editing"><span><?= wfMessage('sng-wikieditguide')->inLanguage($wgLang)->escaped() ?></span></a></li>

				</ul>
			</li>
			<li class="link right account-nav">
				<a class="dropdown-toggle" href="<?php if ($user->isAnon()) { ?><?= Title::newFromText('Special:UserLogin')->fixSpecialName()->getLinkURL() ?><?php } else { ?><?= $user->getUserPage()->getLinkURL() ?><?php } ?>">
					<span class="profile-name"><?php if ($user->isAnon()) { ?><?= wfMessage('sng-notloggedin')->inLanguage($wgLang)->escaped() ?><?php } else { ?><?= htmlspecialchars($user->getName()) ?><?php } ?></span>
				</a>
				<ul class="dropdown">
					<?php foreach ($this->data['personal_urls'] as $key => $tab) { ?>
						<?= $this->getSkin()->makeListItem($key, $tab) ?>

					<?php } ?>

				</ul>
			</li>
		</ul>
	</div>
</div>
<div id="not-nav">
	<div class="logoheader">
				<div class="logopart" href="<?= htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>" title="<?= wfMessage('mainpage')->inLanguage($wgLang)->escaped() ?>">
					<a href="<?= htmlspecialchars($this->data['nav_urls']['mainpage']['href']) ?>">
						<img src="/resources/logo.png" style="height:150px;" title="<?= wfMessage('mainpage')->inLanguage($wgLang)->escaped() ?>">
					</a>
				</div>
			</div>
<div id="view">
	<div class="splash">
		<div class="inner mod-splash">
			<div class="left">
				<div class="box" role="navigation" aria-label="Tools">
					<div class="box-header">
						<h4>Tools</h4>
					</div>
					<div class="box-content">
<?php foreach ($this->data['sidebar']['TOOLBOX'] as $name => $item) { ?>
						<ul>
							<?=$this->getSkin()->makeListItem($name, $item)?>
						</ul>
<?php } ?>

					</div>
				</div>
<?php if ($user->isAnon() && $wgSWS2JoinBox) { ?>
				<div class="box" role="complementary" aria-label="<?=wfMessage( 'scratchwikiskin-helpthewiki' )->inLanguage( $wgLang )->escaped()?>">
					<div class="box-header">
						<h4><?=wfMessage( 'scratchwikiskin-helpthewiki' )->inLanguage( $wgLang )->escaped()?></h4>
					</div>
					<div class="box-content">
						<p><?=wfMessage( 'scratchwikiskin-madeforscratchers')->inLanguage( $wgLang )->parse()?></p>
						<p><a href="<?php echo Title::newFromText(wfMessage( 'scratchwikiskin-becomeacontributor-page' )->inContentLanguage()->text())->getLocalURL();?>"><?=wfMessage( 'scratchwikiskin-learnaboutjoining' )->inLanguage( $wgLang )->escaped()?></a></p>
						<p><a href="<?php echo Title::newFromText(wfMessage( 'portal-url' )->inContentLanguage()->text())->getLocalURL();?>"><?=wfMessage( 'scratchwikiskin-seeportal' )->inLanguage( $wgLang )->escaped()?></a></p>
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
$url = wfMessage('scratchwikiskin-discuss-wiki')->inLanguage( $wgLang )->escaped();
$text = wfMessage('scratchwikiskin-discuss-wiki-text')->inLanguage( $wgLang )->escaped();
$link = "<a href=\"$url\" target=\"_blank\">$text</a>";
$line = wfMessage('scratchwikiskin-dark-theme-feedback')->rawParams( $link )->inLanguage( $wgLang )->escaped();
?>
					<div id="feet" style="margin: 0<?=$darkPref ? '' : '; display: none'?>"><?=$line?></div>
					</div>
				</div>
				<ul id="feet">
<?php foreach ( $this->getFooterLinks('flat') as $key ) { ?>
					<li><?php $this->html( $key ) ?></li>
<?php } ?>
					<?php if (!empty( $wgRightsIcon )) { ?>
					<br/>
					<a href="<?php
					if (!empty( $wgRightsPage )) {
						echo Title::newFromText( $wgRightsPage )->getLocalURL();
					} else {
						echo $wgRightsUrl;
					}
					?>">
						<img style="float: right" alt="<?=$wgRightsText?>" src="<?=$wgRightsIcon?>">
					</a>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
	<div id="footer" role="contentinfo">
		<div class="inner">
			<div class="lists" style="font-weight:bold;margin-bottom:10px">
				<span>
					<a href="https://discord.gg/wqRBSytTmP">Questions or feedback? Join the discussion on the dedicated Discord!</a>
					
				</span>	
			</div>
			<div class="lists">
				<span>
					<a href="/">Wiki Home</a> |
					<a href="https://www.youtube.com/channel/UCpTRuIbbXl0C3503l-GT7Ug">Youtube</a> |
					<a href="https://discord.spellsandguns.com">Discord</a> |
					<a href="/About">About The Wiki</a> |
					<a href="https://spellsandguns.com/">Gaming Wikis</a> |
					<a href="/Term_of_Use">Terms of Use</a> |
					<a href="https://creativecommons.org/licenses/by-sa/4.0/">Copyright Policy</a> |
					<a href="/Special:RecentChanges">Recent Changes</a>
					
				</span>
			</div>
		</div>
	</div>

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
	document.querySelector('div#feet').style.display = '';
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
					if (e.toElement.matches('#navigation .link, #navigation .link>a, #navigation .link>a *')) {
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

document.querySelector('#navigation .sidebar-toggle').addEventListener('click', function(){
	if (window.innerWidth >= 981) return;
	if (!sidebarShown) {
		document.querySelector('#view .inner .left').style.left = '0';
	} else {
		document.querySelector('#view .inner .left').style.left = null;
	}
	sidebarShown = !sidebarShown;
});

</script>

<?php
		if ( $pre139 ) {
			$this->printTrail();
		}
	}
}
