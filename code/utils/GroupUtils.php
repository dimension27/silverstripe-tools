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

	static function findOrCreate( $code, $title ) {
		if( !$rv = self::getByCode($code) ) {
			$rv = new Group();
			$rv->Code = $code;
			$rv->Title = $title;
			$rv->write();
		}
		return $rv;
	}

	static function getByCode( $code ) {
		return DataObject::get_one('Group', "Code = '$code'");
	}

}

?>