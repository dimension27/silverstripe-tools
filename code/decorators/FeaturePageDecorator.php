<?php

/**
 * Example usage:
 * Object::add_extension('HomePage', 'FeaturePageDecorator');
 * // Controls the maximum number of feature items that will be displayed
 * FeaturePageDecorator::$maxNumFeatureItems = 9;
 * // Specifies that the feature items should alway be displayed in multiples of 3
 * FeaturePageDecorator::$forceFeatureItemsMultiple = 3; 
 * @author simonwade
 *
 */
class FeaturePageDecorator extends SiteTreeDecorator {

	// @todo Add $db control over $maxNumFeatureItems and $forceFeatureItemsMultiple
	/**
	 * Controls the maximum number of feature items that will be displayed
	 * @var int
	 */ 
	public static $maxNumFeatureItems = null;

	/**
	 * Specifies that the feature items should alway be displayed in multiples of X
	 * @var int
	 */ 
	public static $forceFeatureItemsMultiple = null;

	public function extraStatics() {
		return array(
			'has_many' => array(
				'FeatureItems' => 'FeaturePageItem.Parent'
			)
		);
	}

	function updateCMSFields( FieldSet $fields ) {
		$fields->addFieldToTab('Root.Content.FeatureItems', $field = new DataObjectManager(
			$this->owner, // controller
			'FeatureItems', // name
			'FeaturePageItem' // sourceClass
		));
		$field->setParentIdName('ParentID');
	}

	function FeatureItems() {
		$items = $this->owner->getComponents('FeatureItems'); /* @var $items DataObjectSet */
		if( self::$maxNumFeatureItems ) {
			$items = $items->getRange(0, self::$maxNumFeatureItems);
		}
		if( self::$forceFeatureItemsMultiple ) {
			$items = $items->getRange(0, floor($items->Count() / self::$forceFeatureItemsMultiple)
					* self::$forceFeatureItemsMultiple);
		}
		return $items;
	}

}

class FeaturePageItem extends DataObject {

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

	public function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Teaser'));
		$fields->addFieldToTab('Root.Main', $field = new ImageUploadField('Image'));
		UploadFolderManager::setUploadFolderForObject($this, $field);
		LinkFields::addLinkFields($fields);
		return $fields;
	}

}

?>