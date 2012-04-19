<?php

/**
 * n.b. This decorator doesn't implement updateCMSFields(), you should add the fields using 
 * LinkFields::addLinkFields();
 * @author simonwade
 */
class LinkFieldsDecorator extends DataObjectDecorator {

	public function extraStatics() {
		return array(
			'db' => array(
				'LinkType' => 'Enum("NoLink, Internal, External, File")',
				'LinkLabel' => 'Varchar(255)',
				'LinkTargetURL' => 'Varchar(255)',
				'OpenInLightbox' => 'Boolean',
			),
			'defaults' => array(
				'LinkType' => 'NoLink',
			),
			'has_one' => array(
				'LinkTarget' => 'SiteTree',
				'LinkFile' => 'File',
			),
		);
	}

	public function LinkURL() {
		return LinkFields::getLinkURL($this->owner);
	}

	public function LinkClass() {
		return (isset($this->owner->LinkClass) ? ' '.$this->owner->LinkClass : '')
				.' '.strtolower(substr($this->owner->LinkType, 0, 1)).substr($this->owner->LinkType, 1)
				.($this->owner->OpenInLightbox ? ' lightbox' : '');
	}

	public function Anchor() {
		if( $url = $this->LinkURL() ) {
			return "<a href='$url' class='".$this->LinkClass()."' "
					."title='".htmlspecialchars('Go to the '.$this->owner->Title)."'>"
					.htmlspecialchars($this->owner->LinkLabel)."</a>";
		}
	}

}

?>