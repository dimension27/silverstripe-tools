<?php

class SiteConfigMetaTags extends DataObjectDecorator {

	static function init( $withSiteTreeSupport = true ) {
		DataObject::add_extension('SiteConfig', 'SiteConfigMetaTags');
		if( $withSiteTreeSupport ) {
			DataObject::add_extension('SiteTree', 'SiteConfigMetaTags_SiteTreeExtension');
		}
	}

	function extraStatics() {
		return array(
			'db' => array(
				'MetaKeywords' => 'Text',
				'MetaDescription' => 'Text'
			)
		);
	}

	function updateCMSFields( $fields ) {
		$fields->addFieldToTab('Root.Main', $field = new TextField('MetaKeywords', 'Keywords - separate with commas'));
		$fields->addFieldToTab('Root.Main', $field = new TextareaField('MetaDescription', 'Description'));
	}

}

class SiteConfigMetaTags_SiteTreeExtension extends SiteTreeDecorator {

	function updateCMSFields( $fields ) {
		$fields->addFieldToTab('Root.Content.Metadata', $field = new HelpField(null, 'Any keywords added here will be added to the global list'), 'MetaDescription');
		$fields->addFieldToTab('Root.Content.Metadata', $field = new HelpField(null, 'If a description is entered here it be used instead of the global description'), 'ExtraMeta');
	}

	function MetaKeywords() {
		$siteConfig = $this->getSiteConfig();
		$keywords = array();
		if( $str = trim($siteConfig->MetaKeywords) ) {
			$keywords[] = $str;
		}
		if( $str = trim($this->owner->MetaKeywords) ) {
			$keywords[] = $str;
		}
		return implode(', ', $keywords);
	}

	function MetaDescription() {
		if( !$rv = trim($this->owner->MetaDescription) ) {
			$rv = $this->getSiteConfig()->MetaDescription;
		}
		return $rv;
	}

	function MetaTags( &$tags ) {
		$tags = '';
		if($str = $this->MetaKeywords()) {
			$tags .= "<meta name=\"keywords\" content=\"" . Convert::raw2att($str) . "\" />\n";
		}
		if($str = $this->MetaDescription()) {
			$tags .= "<meta name=\"description\" content=\"" . Convert::raw2att($str) . "\" />\n";
		}
		if($str = $this->owner->ExtraMeta) {
			$tags .= $str . "\n";
		}
	}

	function getSiteConfig() {
		return SiteConfig::current_site_config();
	}

}