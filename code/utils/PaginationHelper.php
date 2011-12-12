<?php

class PaginationHelper {

	/**
	 * @var DataObjectSet
	 */
	protected $set;
	protected $limitPerPage;
	protected $offset;

	function __construct( DataObjectSet $set, $limitPerPage = null ) {
		$this->set = $set;
		if( $limitPerPage ) {
			$this->limitPerPage = $limitPerPage;
		}
	}

	function LimitedResults( $limitPerPage = null ) {
		if( $limitPerPage ) {
			$this->limitPerPage = $limitPerPage;
		}
		$this->offset = isset($_GET['start']) ? (int) $_GET['start'] : 0;
		return $this->set->getRange($this->offset, $this->limitPerPage);
	}

	function AllResults() {
		$this->set->setPageLimits($this->offset, $this->limitPerPage, $this->set->Count());
		return $this->set;
	}

}

?>