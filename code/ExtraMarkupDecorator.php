<?php 

/**
 * Adds an "Extra Markup" field to SiteTree, which allows arbitrary markup to be appended to the 
 * content of the page. For example, this can be used to add conversion tracking code to pages.
 * nb. This relies on overriding the Content() method of the SiteTree, so if there are any other
 * modules that rely on this (eg. HideEmail) then the ExtraMarkup will need to be handled by 
 * adding "$ExtraMarkup.Raw" to the Page.ss and adding the following method to the Page_Controller class:
 * 
 * class Page_Controller {
 *   ...
 *   function ExtraMarkup() {
 *   	return ExtraMarkupDecorator::get_extra_markup($this);
 *   }
 * }
 * 
 */

class ExtraMarkupDecorator extends SiteTreeDecorator {

	public static $field_label = 'Extra Markup - This is appended to the content of the page';

	public function extraStatics() {
		return array(
			'db' => array(
				'ExtraMarkup' => 'Text',
				'ExtraMarkupAllowPHP' => 'Boolean'
			)
		);
	}

	public function updateCMSFields( $fields ) {
		$fields->addFieldToTab('Root.Content.Metadata',  new TextareaField('ExtraMarkup', self::$field_label));
		$fields->addFieldToTab('Root.Content.Metadata',  new CheckboxField('ExtraMarkupAllowPHP', 'Allow PHP code in '.self::$field_label));
	}

	function Content() {
		$content = $this->owner->getField('Content');
		return $content.self::get_extra_markup($this->owner);
	}

	static function get_extra_markup( $dataObject ) {
		$rv = $dataObject->ExtraMarkup;
		if( $dataObject->ExtraMarkupAllowPHP ) {
			$phpCode =<<<EOB
?>
$rv
EOB;
			ob_start();
			eval($phpCode);
			$rv = ob_get_clean();
		}
		return $rv;
	}
	
}

?>