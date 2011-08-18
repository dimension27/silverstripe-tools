<?php
class SSTools_Scheduling_Schedule extends Object {
	
	protected $start = null;
	protected $end = null;
	
	public $title = 'Schedule';
	
	protected $events = array();
	
	public function addEvent(SSTools_Scheduling_Event $event) {
		if (!$start = $event->start()) {
			throw new Exception('Event '.$event.' has no start date');
		}
		if (!$end = $event->end()) {
			throw new Exception('Event '.$event.' has no end date');
		}
		$this->events[] = $event;
		if (is_null($this->start) || $start < $this->start) {
			$this->start = clone($start);
		}
		if (is_null($this->end) || $end > $this->end) {
			$this->end = clone($end);
		}
		$this->sortEvents();
	}
	
	public function getTitle($includeDates = true) {
		$rv = '';
		if ($this->title) {
			$rv = $this->title;
		}
		if ($includeDates) {
			$
			
		}
		return $rv;
	}
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 *  Used internally to keep the events array in order.
	 */
	protected function sortEvents() {
		usort($this->events, array($this, 'compareEvents'));
	}
	
	protected function compareEvents($a, $b) {
		$dateA = $a->start();
		$dateB = $b->start();
		if ($dateA < $dateB) {
			return -1;
		}
		elseif ($dateA == $dateB) {
			return 0;
		}
		return 1;
	}
	
	public function getEvents() {
		return $this->events;
	}
}