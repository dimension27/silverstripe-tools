<?php

/**
 * Example usage:
 * Object::add_extension('Page', 'LinkListDecorator');
 * // Controls the maximum number of items that will be displayed
 * LinkListDecorator::$maxNumItems = 9;
 * // Specifies that the items should alway be displayed in multiples of 3
 * LinkListDecorator::$forceMultiple = 3; 
 * @author simonwade
 */
class LinkListDecorator extends DataObjectDecorator {

	// @todo Add $db control over $maxNumItems and $forceMultiple
	/**
	 * Controls the maximum number of items that will be displayed
	 * @var int
	 */ 
	public static $maxNumItems = null;

	/**
	 * Specifies that the items should alway be displayed in multiples of X
	 * @var int
	 */ 
	public static $forceMultiple = null;

	static $tabName = 'Root.Content.LinkedItems';
	static $relationshipName = 'LinkListItems';
	static $itemClassName = 'LinkListDecorator_Item';

	function getItem() {
		$className = $this->stat('itemClassName');
		return new $className;
	}

	function LimitLinkListItems( $maxNumItems = null, $forceMultiple = null ) {
		$items = $this->owner->getComponents(self::$relationshipName); /* @var $items DataObjectSet */
		return $this->handleItemSet($items,  $maxNumItems, $forceMultiple);
	}

	public function extraStatics() {
		return array(
			'has_many' => array(
				'LinkListItems' => 'LinkListDecorator_Item.Parent'
			)
		);
	}

	function updateCMSFields( FieldSet $fields ) {
		$this->addManager(
			$fields,
			$this->stat('tabName'),
			$this->stat('relationshipName'),
			$this->stat('itemClassName')
		);
	}

	function addManager( FieldSet $fields, $tabName, $relationshipName, $className ) {
		$fields->addFieldToTab(
			$tabName,
			$field = new DataObjectManager(
				$this->owner, // controller
				$relationshipName, // name
				$className // sourceClass
			)
		);
		$field->setAddTitle($this->getItem()->stat('singular_name'));
		$field->setParentIdName('ParentID');
	}

	function handleItemSet( $items, $maxNumItems = null, $forceMultiple = null ) {
		if( $maxNumItems || ($maxNumItems = $this->stat('maxNumItems')) ) {
			$items = $items->getRange(0, $maxNumItems);
		}
		else if( $forceMultiple || ($forceMultiple = $this->stat('forceMultiple')) ) {
			$items = $items->getRange(0, floor($items->Count() / $forceMultiple) * $forceMultiple);
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

	static $singular_name = 'Link';
	static $plural_name = 'Links';
	
	public function getCMSFields() {
		$fields = FormUtils::createMain();
		LinkFields::addLinkFields($fields);
		return $fields;
	}

}

?>