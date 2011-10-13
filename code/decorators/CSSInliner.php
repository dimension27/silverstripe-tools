<?php

class CSSInliner extends DataObjectDecorator {

	static $css = '';
	static $replacements = array();

	static function addCSS( $css ) {
		self::$css .= $css;
	}

	static function addCSSFile( $path ) {
		$path = Director::baseFolder().'/'.$path;
		self::$css .= file_get_contents($path);
	}

	static function addReplacement( $regExp, $replace ) {
		self::$replacements[$regExp] = $replace;
	}

	function InlineCSS( $field = null ) {
		$emogrifier = new Emogrifier();
		$delimiter = '__InlineCSSDelimiter__';
		$html = '<html><body>'.$delimiter.$this->owner->$field.$delimiter.'</body></html>';
		$emogrifier->setHTML($html);
		$emogrifier->setCSS(self::$css);
		$rv = $emogrifier->emogrify();
		preg_match("/$delimiter(.*)$delimiter/s", $rv, $matches);
		$rv = $matches[1];
		if( self::$replacements ) {
			$rv = preg_replace(array_keys(self::$replacements), array_values(self::$replacements), $rv);
		}
		return $rv;
	}

}

?>