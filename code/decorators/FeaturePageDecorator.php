<?php

/**
 * Example usage:
 * Object::add_extension('HomePage', 'FeaturePageDecorator');
 * // Controls the maximum number of items that will be displayed
 * FeaturePageDecorator::$maxNumItems = 9;
 * // Specifies that the items should alway be displayed in multiples of 3
 * FeaturePageDecorator::$forceItemsMultiple = 3; 
 * @author simonwade
 *
 */
class FeaturePageDecorator extends LinkListDecorator {

	/**
	 * Controls the maximum number of items that will be displayed
	 * @var int
	 */ 
	public static $maxNumItems = null;

	/**
	 * Specifies that the items should alway be displayed in multiples of X
	 * @var int
	 */ 
	public static $forceItemsMultiple = null;

	static $relationshipName = 'FeatureItems';
	static $itemClassName = 'FeaturePageDecorator_Item';

	public function extraStatics() {
		return array(
			'has_many' => array(
				'FeatureItems' => 'FeaturePageDecorator_Item.Parent'
			)
		);
	}

	function FeatureItems() {
		$items = $this->owner->getComponents(self::$relationshipName); /* @var $items DataObjectSet */
		return $this->handleItemSet($item);
	}

}

class FeaturePageDecorator_Item extends DataObject {

	static $db = array(
		'Title' => 'Varchar(255)',
		'Teaser' => 'HTMLText',
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkLabel' => 'Varchar(255)',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean',
	);

	static $has_one = array(
		'Parent' => 'SiteTree',
		'Image' => 'BetterImage',
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File',
	);

	static $summary_fields = array(
		'Title'
	);

	static $plural_name = 'Feature Item';

	public function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Teaser'));
		$fields->addFieldToTab('Root.Main', $field = new ImageUploadField('Image'));
		UploadFolderManager::setUploadFolder($this, $field);
		LinkFields::addLinkFields($fields, null, 'Root.Link');
		return $fields;
	}

}

UploadFolderManager::setOptions('FeaturePageDecorator_Item', array(
	'folder' => 'Uploads/FeatureItems',
	'date' => null
));

?>