<?php

if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'vhv' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['vhv'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['vhvMagic'] = __DIR__ . '/vhv.i18n.magic.php';
	/* wfWarn(
		'Deprecated PHP entry point used for FooBar skin. Please use wfLoadSkin instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return true;
} else {
	die( 'This version of the vhv skin requires MediaWiki 1.25+' );
}
