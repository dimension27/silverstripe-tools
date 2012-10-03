<?php

class StringUtils {

	public static function createReplacements( $values ) {
		$rv = array();
		foreach( $values as $name => $value ) {
			$rv["{\$$name}"] = $value;
		}
		return $rv;
	}

	function handleReplacements( $content, $replacements, $extra = array() ) {
		if( $extra ) {
			$replacements = array_merge($replacements, $extra);
		}
		return str_replace(array_keys($replacements), array_values($replacements), $content);
	}

}