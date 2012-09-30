<?php

class StringUtils {

	function handleReplacements( $content, $replacements, $extra = array() ) {
		if( $extra ) {
			$replacements = array_merge($replacements, $extra);
		}
		return str_replace(array_keys($replacements), array_values($replacements), $content);
	}

}