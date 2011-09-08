<?php

/**
 * Needs to be enabled using FAQPage::setAllowCreate()
 * @author simonwade
 */
class FAQPage extends Page {

	protected static $allow_create = false;

	public static function setAllowCreate( $bool = true ) {
		self::$allow_create = $bool;
	}

	public function canCreate( $member = null ) {
		return self::$allow_create && parent::canCreate($member);
	}

	static $has_many = array(
		'Items' => 'FAQPage_Item'
	);
	static $singular_name = 'FAQ Page';

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$field = $fields->fieldByName('Root.Content.Main.Content'); /* @var $field HTMLEditorField */
		$field->setRows(5);
		$fields->addFieldToTab('Root.Content.Main', $field = new HeaderField('FAQ Items'));
		$fields->addFieldToTab('Root.Content.Main', $field = new DataObjectManager(
			$this, // controller
			'Items', // name
			'FAQPage_Item' // sourceClass
		));
		$field->setAddTitle('FAQ Item');
		$field->setSingleTitle('FAQ Item');
		return $fields;
	}

}

class FAQPage_Controller extends Page_Controller {

}

class FAQPage_Item extends DataObject {

	static $db = array(
		'Title' => 'Varchar',
		'Content' => 'HTMLText',
	);

	static $has_one = array(
		'Page' => 'FAQPage'
	);

	static $singular_name = 'FAQ Item';

	public function getCMSFields() {
		return new FieldSet(array(
			new TextField('Title'),
			$field = new SimpleTinyMCEField('Content')
		));
		// @todo: Should add the support for inheriting the TinyMCE config from HTMLEditorConfig
		// as per HTMLBodyRecipientDecorator
	}

	public function UniqueID() {
		return $this->ID;
	}

}

?>