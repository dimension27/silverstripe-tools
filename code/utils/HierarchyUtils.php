<?php

class HierarchyUtils {

	static function getDescendants( $dataObject, $className, $sort ) {
		$parentIDs = $dataObject->getDescendantIDList();
		$parentIDs[] = $dataObject->ID;
		return DataObject::get($className, "ParentID IN ('".implode($parentIDs, "', '"). "')", $sort);
	}

}

?>