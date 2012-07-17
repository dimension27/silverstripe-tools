<?php

class GroupUtils {

	static function getPath( $group, $field = 'Code', $separator = '/' ) {
		$parents = array();
		do {
			$parents[] = $group->$field;
			$group = $group->Parent();
		}
		while( $group->exists() );
		return implode($separator, array_reverse($parents));
	}

}
?>