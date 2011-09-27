<?php
define('SS_TOOLS_DIR', 'ss-tools');

if( Director::is_cli() ) {
	Director::addRules(50, array(
		'ss-tools' => 'SSToolsController',
	));
}

Object::add_extension('LinkListDecorator_Item', 'LinkFieldsDecorator');
SortableDataObject::add_sortable_class('LinkListDecorator_Item');
Object::add_extension('FeaturePageDecorator_Item', 'LinkFieldsDecorator');
SortableDataObject::add_sortable_class('FeaturePageDecorator_Item');

?>