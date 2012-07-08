<?php

class LinkFields {

	/**
	 * See LinkFieldsDecorator for the fields that are required for persistence.
	 * You can use the LinkFields::getLinkURL() method to return the link URL.
	 * @param Fieldset $fields
	 * @param array $options
	 * @param string $tabName
	 */
	static function addLinkFields( $fields, $options = null, $tabName = 'Root.Main' ) {
		if( @$options['label'] ) {
			$fields->addFieldToTab($tabName, new HeaderField($options['label'], null, 3));
		}
		$fields->addFieldToTab($tabName, $field = new TextField('LinkLabel', 'Link label'));
		// Install the urlfield module for URL validation: git://github.com/chillu/silverstripe-urlfield.git
		$urlClass = class_exists('URLField') ? 'URLField' : 'TextField';
		$fields->addFieldToTab($tabName, $group = new SelectionGroup('LinkType', array(
				'NoLink//No link' => new LiteralField('NoLink', ''),
				'Internal//Link to a page on this website' => new TreeDropdownField('LinkTargetID', 'Link target', 'SiteTree'),
				'External//Link to an external website' => new $urlClass('LinkTargetURL', 'Link target URL'),
				'File//Download a file' => new TreeDropdownField('LinkFileID', 'Download file', 'File')
		)));
		if( @$options['openInLightbox'] ) {
			$fields->addFieldToTab($tabName, new CheckboxField('OpenInLightbox', 'Open the link in a lightbox'));
		}
	}

	static function getLinkURL( $obj ) {
		switch( $obj->LinkType ) {
			case 'NoLink':
				return '';
			case 'External':
				return $obj->LinkTargetURL;
			case 'Internal':
				if( ($target = $obj->LinkTarget()) && $target->exists() ) {
					return $target->Link();
				}
				break;
			case 'File':
				if( ($target = $obj->LinkFile()) && $target->exists() ) {
					return $target->Link();
				}
				break;
		}
	}

}

?>