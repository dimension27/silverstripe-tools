<?php
class SSTools_Scheduling_Event extends Object {
	protected $start = null;
	protected $end = null;
	
	protected $data = array();
	
	public function __construct(DateTime $start = nul, DateTime $end = null) {
		$this->start = $start;
		$this->end = $end;
	}
	
	public function addData($data, $key = null) {
		if (is_null($key)) {
			$this->data[] = $data;
		}
		else {
			$this->data[$key] = $data;
		}
	}
}