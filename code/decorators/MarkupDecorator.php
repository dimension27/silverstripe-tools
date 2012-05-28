<?php

class MarkupDecorator extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
				'Markup_InheritFromParent' => 'Boolean',
			),
			'has_many' => array(
				'MarkupItems' => 'MarkupDecorator_Item',
			),
			'defaults' => array(
				'Markup_InheritFromParent' => true,
			)
		);
	}

	function updateCMSFields( FieldSet $fields ) {
		$fields->addFieldToTab('Root.Markup', $field = new DataObjectManager(
			$this->owner, 'MarkupItems', 'MarkupDecorator_Item'
		));
		// for some reason this is required for SiteConfig support
		$field->setSourceID($this->owner->ID);
		if( $this->owner instanceof SiteTree ) {
			$fields->addFieldToTab('Root.Markup', $field = new CheckboxField('Markup_InheritFromParent', 'Inherit from parent page'));
		}
	}

	function MarkupItemsFor( $location ) {
		$request = Controller::curr()->getRequest(); /* @var $request SS_HTTPRequest */
		$filter = "Location = '$location'";
		$top = $parent = $this->owner;
		$set = new DataObjectSet();
		do {
			$this->addItems($filter, $set, $parent, ($parent !== $this->owner), $request);
			$top = $parent;
		}
		while( $parent->Markup_InheritFromParent
				&& ($parent = $parent->Parent())
				&& $parent->ID );
		if( $top->Markup_InheritFromParent ) {
			if( ($siteConfig = SiteConfig::current_site_config())
					&& $siteConfig->hasMethod('MarkupItems') ) {
				$this->addItems($filter, $set, $siteConfig, true, $request);
			}
		}
		return $set;
	}

	protected function addItems( $filter, $set, $dataObject, $forChild, $request ) {
		foreach( $dataObject->MarkupItems($filter) as $item ) {
			if( !$forChild || $item->AddToChildren ) {
				if( $dataObject instanceof UserDefinedForm ) {
					if( !$item->OnlyOnComplete || ($request->param('Action') == 'finished') ) {
						$set->push($item);
					}
				}
				else {
					$set->push($item);
				}
			}
		}
	}

	function MarkupFor( $location ) {
		$rv = '';
		foreach( $this->owner->MarkupItemsFor($location) as $item ) {
			$rv .= $item->Markup."\n";
		}
		return $rv;
	}
}

class MarkupDecorator_Item extends DataObject {

	static $db = array(
		'Title' => 'Varchar(255)',
		'Markup' => 'HTMLText',
		'Location' => 'Enum("Head,Top,Bottom")',
		'AddToChildren' => 'Boolean',
		'OnlyOnComplete' => 'Boolean',
	);

	static $singular_name = 'Markup';

	static $has_one = array(
		'SiteConfig' => 'SiteConfig',
		'SiteTree' => 'SiteTree',
	);

	static $summary_fields = array('Title', 'Location');

	function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new TextareaField('Markup'));
		$fields->addFieldToTab('Root.Main', $field = FormUtils::getEnumDropdown($this, 'Location'));
		$fields->addFieldToTab('Root.Main', $field = new CheckboxField('AddToChildren', 'Cascade down to child pages'));
		if( $this->getParent() instanceof UserDefinedForm ) {
			$fields->addFieldToTab('Root.Main', $field = new CheckboxField('OnlyOnComplete', 'Only display on complete'));
		}
		return $fields;
	}

	function getParent() {
		return $this->SiteConfigID ? $this->SiteConfig() : $this->SiteTree();
	}

}

