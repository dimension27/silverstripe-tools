<?php
define('SS_TOOLS_DIR', 'ss-tools');

if( Director::is_cli() ) {
	Director::addRules(50, array(
		'ss-tools' => 'SSToolsController',
	));
}

SortableDataObject::add_sortable_class('LinkListDecorator_Item');
SortableDataObject::add_sortable_class('FeaturePageDecorator_Item');

ShortcodeParser::get()->register('YouTube', array('Page','YouTubeShortCodeHandler'));
?>