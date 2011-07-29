<?php
define('SS_TOOLS_DIR', 'ss-tools');

if( Director::is_cli() ) {
	Director::addRules(50, array(
		'ss-tools' => 'SSToolsController',
	));
}
?>
