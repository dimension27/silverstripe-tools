<?php

class PaginationHelper {

	/**
	 * @var DataObjectSet
	 */
	public $set;
	public $numResults;
	public $limitPerPage;
	public $offset;

	function __construct( DataObjectSet $set, $limitPerPage = null ) {
		$this->set = $set ? $set : new DataObjectSet();
		$this->numResults = $this->set->Count();
		if( $limitPerPage ) {
			$this->limitPerPage = $limitPerPage;
		}
		$this->offset = isset($_GET['start']) ? (int) $_GET['start'] : 0;
	}

	function LimitedResults( $limitPerPage = null ) {
		if( $limitPerPage ) {
			$this->limitPerPage = $limitPerPage;
		}
		return $this->set->getRange($this->offset, $this->limitPerPage);
	}

	function AllResults() {
		$this->set->setPageLimits(
			$this->offset, $this->limitPerPage, $this->numResults
		);
		return $this->set;
	}

	function PreviousLink( $url ) {
		$offset = ($this->offset - $this->limitPerPage);
		if( $offset >= 0 ) {
			return $this->Link($url, $offset);
		}
	}

	function NextLink( $url ) {
		$offset = $this->offset + $this->limitPerPage;
		if( $offset < $this->numResults ) {
			return $this->Link($url, $offset);
		}
	}

	function Link( $url, $offset ) {
		return $url.(strpos($url, '?') === false ? '?' : '&').'start='.$offset;
	}

	function getPreviousItem() {
		$offset = $this->offset - 1;
		if( ($offset >= 0) && $this->numResults ) {
			return $this->set->getRange($offset, 1)->pop();
		}
	}

	function getNextItem() {
		$offset = $this->offset + 1;
		if( $offset < $this->numResults ) {
			return $this->set->getRange($offset, 1)->pop();
		}
	}

}

?>