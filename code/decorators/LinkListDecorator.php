<?php

/**
 * Example usage:
 * Object::add_extension('Page', 'LinkListDecorator');
 * // Controls the maximum number of items that will be displayed
 * LinkListDecorator::$maxNumItems = 9;
 * // Specifies that the items should alway be displayed in multiples of 3
 * LinkListDecorator::$forceItemsMultiple = 3; 
 * @author simonwade
 */
class LinkListDecorator extends DataObjectDecorator {

	// @todo Add $db control over $maxNumItems and $forceItemsMultiple
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

	static $relationshipName = 'LinkListItems';
	static $itemClassName = 'LinkListDecorator_Item';

	function LinkListItems() {
		$items = $this->owner->getComponents(self::$relationshipName); /* @var $items DataObjectSet */
		return $this->handleItemSet($item);
	}

	public function extraStatics() {
		return array(
			'has_many' => array(
				'LinkListItems' => 'LinkListDecorator_Item.Parent'
			)
		);
	}

	function updateCMSFields( FieldSet $fields ) {
		$this->addManager($fields, self::$relationshipName, self::$itemClassName);
	}

	function addManager( FieldSet $fields, $relationshipName, $className ) {
		if( $this->owner instanceof SiteConfig ) {
			$tabName = "Root.$relationshipName";
		}
		else {
			$tabName = "Root.Content.$relationshipName";
		}
		$fields->addFieldToTab($tabName, $field = new DataObjectManager(
			$this->owner, // controller
			$relationshipName, // name
			$className // sourceClass
		));
		$field->setAddTitle(Object::get_static($className, 'plural_name'));
		$field->setParentIdName('ParentID');
	}

	function handleItemSet( $items ) {
		if( self::$maxNumItems ) {
			$items = $items->getRange(0, self::$maxNumItems);
		}
		if( self::$forceItemsMultiple ) {
			$items = $items->getRange(0, floor($items->Count() / self::$forceItemsMultiple)
					* self::$forceItemsMultiple);
		}
		return $items;
	}

}

class LinkListDecorator_Item extends DataObject {

	static $db = array(
		'LinkType' => 'Enum("Internal, External, File")',
		'LinkLabel' => 'Varchar(255)',
		'LinkTargetURL' => 'Varchar(255)',
		'OpenInLightbox' => 'Boolean',
	);

	static $has_one = array(
		'Parent' => 'SiteTree',
		'LinkTarget' => 'SiteTree',
		'LinkFile' => 'File',
	);

	static $summary_fields = array(
		'LinkLabel'
	);

	static $plural_name = 'Link';

	public function getCMSFields() {
		$fields = FormUtils::createMain();
		LinkFields::addLinkFields($fields);
		return $fields;
	}

}

?>