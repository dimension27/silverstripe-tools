<?php
/**
 * Example usage:
 * 
 * Object::add_extension('HomePage', 'FeaturePageDecorator');
 * // Controls the maximum number of items that will be displayed
 * FeaturePageDecorator::$maxNumItems = 9;
 * // Specifies that the items should alway be displayed in multiples of 3
 * FeaturePageDecorator::$forceItemsMultiple = 3; 
 * 
 * Template example:
 *
 *	<ul id="featured-items">
 *	<% control LimitFeatureItems(3) %>
 *	    <li>
 *	    	<% if LinkURL %><a href="$LinkURL" title="$LinkLabel">$Image.SetCroppedSize(237, 168)</a>
 *	    	<% else %>$Image.SetCroppedSize(237, 168)<% end_if %>
 *	</li>
 *	<% end_control %>
 *	</ul>
 *
 * @author simonwade
 */
class FeaturePageDecorator extends LinkListDecorator {

	static $tabName = 'Root.Content.FeatureItems';
	static $relationshipName = 'FeatureItems';
	static $itemClassName = 'FeaturePageDecorator_Item';

	public function extraStatics() {
		return array(
			'has_many' => array(
				'FeatureItems' => 'FeaturePageDecorator_Item.Parent'
			)
		);
	}

	function LimitFeatureItems( $maxNumItems = null, $forceMultiple = null ) {
		$items = $this->owner->getComponents(self::$relationshipName); /* @var $items DataObjectSet */
		return $this->handleItemSet($items,  $maxNumItems, $forceMultiple);
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

	static $singular_name = 'Feature Item';
	static $plural_name = 'Feature Items';
	static $disabled_fields = array();
	static $extensions = array('LinkFieldsDecorator');

	public function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Teaser'));
		$fields->addFieldToTab('Root.Main', $field = new ImageUploadField('Image'));
		UploadFolderManager::setUploadFolder($this, $field);
		LinkFields::addLinkFields($fields, null, 'Root.Link');
		foreach( self::$disabled_fields as $name => $disabled ) {
			if( $disabled ) {
				$fields->removeByName($name);
			}
		}
		return $fields;
	}

	public static function disable_field( $field, $bool = true ) {
		self::$disabled_fields[$field] = $bool;
	} 

	public function LinkLabel() {
		return $rv = $this->LinkLabel ? $rv : $this->Title;
	}

	public function onAfterWrite() {
		parent::onAfterWrite();
		$file = $this->Image();
		if( $file && $file->exists() ) {
			$file->Title = $this->Title;
			$file->write();
		}
	}

}

UploadFolderManager::setOptions('FeaturePageDecorator_Item', array(
	'folder' => 'Uploads/FeatureItems',
	'date' => null
));

?>