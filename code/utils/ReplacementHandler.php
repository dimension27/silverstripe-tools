<?php
/**
 * Example usage in a Controller:
 *
 *     class MyController extends Page_Controller {
 *       public function init() {
 *         parent::init();
 *         $this->replacements = new ReplacementHandler($this);
 *         // perform replacements in...
 *         $this->replacements->fields = array('Content', 'SecondaryContent');
 *         // replace the following values using {{Foobar}}, {{Schedule}} and {{Categories}}
 *         $this->replacements->replacements = array('Foobar', 'Schedule', 'Categories');
 *         // Foobar will come from $this->Foobar, and we'll set Schedule and Categories below
 *         $this->replacements->setReplacementValue('Schedule', $this->newViewer('Schedule')->process($this));
 *         $this->replacements->setReplacementValue('Categories', $this->newViewer('Categories')->process($this));
 *       }
 *       public function newViewer($templateList) {
 *         return new SSViewer($templateList);
 *       }
 *       public function index() {
 *         return $this->replacements->process();
 *       }
 *     }
 * 
 */
class ReplacementHandler {
	public $data = null;
	public $casting = true;
	public $fields = array('Content');
	public $found = array();
	public $replacements = array();
	public $replacementValues = array();
	public function __construct( $data, $fields = null, $replacements = null ) {
		$this->data = $data;
		if( $fields ) $this->fields = $fields;
		if( $replacements ) $this->replacements = $replacements;
	}
	public function process() {
		$this->found = array();
		foreach( $this->fields as $field ) {
			$replaced = array(
				$field => $this->data->$field
			);
		}
		foreach( $this->fields as $field ) {
			if( !isset($replaced[$field]) ) {
				$replaced[$field] = $this->data->$field;
				$found[$field] = array();
			}
			foreach( $this->replacements as $replacement ) {
				$value = $replaced[$field];
				$search = '{{'.$replacement.'}}';
				if( stripos($replaced[$field], $search) !== false ) {
					$found[$field][$replacement] = true;
					$replaceWith = $this->getReplacementValue($replacement);
					$value = str_ireplace($search, $replaceWith, $value);
				}
				$replaced[$field] = $value;
			}
		}
		if( $this->casting ) {
			foreach( $replaced as $field => $value ) {
				$replaced[$field] = DBField::create('HTMLText', $value);
			}
		}
		return $replaced;
	}
	public function setReplacementValue( $replacement, $value ) {
		$this->replacementValues[$replacement] = $value;
	}
	public function getReplacementValue( $replacement ) {
		return isset($this->replacementValues[$replacement])
				? $this->replacementValues[$replacement]
				: $this->data->$replacement;
	}
	public function found( $replacement, $inField ) {
		return $this->found[$inField][$replacement];
	}
}

?>