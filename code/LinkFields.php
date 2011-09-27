<?php

class LinkFields {

	/**
	 * Requires the following fields for persistence:
	 *   static $db => array(
	 *     'LinkType' => 'Enum("Internal, External, File")',
	 *     'LinkLabel' => 'Varchar(255)',
	 * 	   'LinkTargetURL' => 'Varchar(255)'
	 *   );
	 *   static $has_one => array(
	 *     'LinkTarget' => 'SiteTree',
	 * 	   'LinkFile' => 'File'
	 *   );
	 * If the openInLightbox option is used then also need:
	 *   static $db => array(
	 *     'OpenInLightbox' => 'Boolean'
	 *   );
	 * You can use the LinkFields::getLink() method to render the HTML.
	 * @param Fieldset $fields
	 * @param array $options
	 * @param string $tabName
	 */
	static function addLinkFields( $fields, $options = null, $tabName = 'Root.Main' ) {
		if( @$options['label'] ) {
			$fields->addFieldToTab($tabName, new HeaderField($options['label'], null, 3));
		}
		$fields->addFieldToTab($tabName, $field = new TextField('LinkLabel', 'Link label'));
		// Install the urlfield module for URL validation git://github.com/chillu/silverstripe-urlfield.git
		$urlClass = class_exists('URLField') ? 'URLField' : 'TextField';
		$fields->addFieldToTab($tabName, $group = new SelectionGroup('LinkType', array(
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
			case 'External':
				return $obj->LinkTargetURL;
			case 'Internal':
				if( ($target = $obj->LinkTarget()) && $target->exists() ) {
					return $target->Link();
				}
				else {
					return $obj->LinkTargetURL;
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

class LinkFieldsDecorator extends DataObjectDecorator {

	public function Link() {
		return LinkFields::getLinkURL($this->owner);
	}

	public function LinkClass() {
		return (isset($this->owner->LinkClass) ? ' '.$this->owner->LinkClass : '')
				.' '.strtolower(substr($this->LinkType, 0, 1)).substr($this->LinkType, 1)
				.($this->OpenInLightbox ? ' lightbox' : '');
	}

	public function Anchor() {
		if( $url = $this->Link() ) {
			return "<a href='$url' class='".$this->LinkClass()."' "
					."title='".htmlspecialchars('Go to the '.$this->Title)."'>"
					.htmlspecialchars($this->LinkLabel)."</a>";
		}
	}

}

?>