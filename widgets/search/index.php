<?php
// widgets/search/index.php
if (defined('MAIN_INIT')) {
	$patt = array('/[\t\r]/', '/{(LNG_[A-Z0-9_]+)}/e', '/{WEBURL}/', '/{SEARCH}/', '/{ID}/');
	$replace = array();
	$replace[] = '';
	$replace[] = OLD_PHP ? '$lng[\'$1\']' : 'gcms::getLng';
	$replace[] = WEB_URL;
	$replace[] = preg_replace('/[\+\s]+/u', ' ', gcms::getVars($_GET, 'q', ''));
	$replace[] = gcms::rndname(10);
	$widget = gcms::pregReplace($patt, $replace, file_get_contents(ROOT_PATH.'widgets/search/search.html'));
}
