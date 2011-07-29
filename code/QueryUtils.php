<?php
class QueryUtils {
	/**
	 * Returns a 'count' of results in the given $query, optionally for a given $distinctCountField.
	 * @param SQLQuery $query
	 * @param string $distinctCountField
	 */
	public static function getCount(SQLQuery $query, $distinctCountField = null) {
		$query->select(
			'count('.(is_null($distinctCountField)? '*' : 'distinct `'.$distinctCountField.'`').") as 'result_count'"
		);
		if ($result = $query->execute()->First()) {
			return $result['result_count'];
		}
	}
}