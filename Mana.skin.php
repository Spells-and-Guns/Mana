<?php

/**
 * SkinTemplate class for Mana
 *
 * @file
 * @ingroup Skins
 */

require_once __DIR__ . '/consts.php';

class Mana extends SkinTemplate {
	var $skinname = 'mana', $stylename = 'Mana',
		$template = 'ManaTemplate', $useHeadElement = true;

	/**
	 * @inheritDoc
	 */
	public function __construct($options) {
		$options['bodyOnly'] = true;
		parent::__construct($options);
	}

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */

	public function initPage(OutputPage $out) {
		parent::initPage($out);
		$out->addModuleStyles([
			'skins.mana'
		]);
		// make Chrome mobile testing work
		$out->addMeta('viewport', 'user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height');
	}
}
