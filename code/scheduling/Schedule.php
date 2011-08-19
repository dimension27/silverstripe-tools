<?php
class SSTools_Scheduling_Schedule extEnds ViewableData {
	
	protected $Start = null;
	protected $End = null;
	
	public $Title = 'Schedule';
	
	protected $events = array();
	
	 /**
	  * Creates a new instance.
	  * @param mixed $Start Either a DateTime, or a DateInterval to subtract from End.
	  * @param mixed $End. Either a DateTime, or a DateInterval to add to Start.
	  */
	public function __construct($Start = null, $End = null) {
		parent::__construct();
		$StartIsInterval = false;
		if (is_null($Start)) {
			$Start = new DateTime();
		}
		else {
			$StartIsInterval = $Start instanceof DateInterval;
		}
		
		$EndIsInterval = false;
		if (is_null($End)) {
			$End = new DateTime();
		}
		else {
			$EndIsInterval = $End instanceof DateInterval;
		}
		
		if ($StartIsInterval && $EndIsInterval){
			throw new Exception('Start and End are both DateIntervals');
		}
		if ($StartIsInterval) {
			$interval = $Start;
			$Start = clone($End);
			$Start->sub($interval);
		}
		elseif ($EndIsInterval) {
			$interval = $End;
			$End = clone($Start);
			$End->add($interval);
		}
		$this->Start = $Start;
		$this->End = $End;
	}
	
	public function add(SSTools_Scheduling_Event $event) {
		if (!$Start = $event->Start) {
			throw new Exception('Event '.$event.' has no Start date');
		}
		if (!$End = $event->End) {
			throw new Exception('Event '.$event.' has no End date');
		}
		$this->events[] = $event;
		if ($Start < $this->Start) {
			$this->Start = clone($Start);
		}
		if ($End > $this->End) {
			$this->End = clone($End);
		}
		$this->sortEvents();
	}
	
	public function getEvents() {
		return $this->events;
	}
	
	public function getEnd() {
		return $this->End;
	}
	
	public function getStart() {
		return $this->Start;
	}
	
	public function getTitle($includeDates = true, $dateFormat = null) {
		$rv = '';
		if ($this->Title) {
			$rv = $this->Title;
		}
		if ($includeDates) {
			if (is_null($dateFormat)) {
				$dateFormat = 'd/m/Y';
			}
			$dates = $this->Start->format($dateFormat).' - '.$this->End->format($dateFormat);
			$rv = strlen($rv) ? $rv.' ('.$dates.')' : $dates;
		}
		return $rv;
	}
	
	public function setTitle($Title) {
		$this->Title = $Title;
	}
	
	/**
	 *  Used internally to keep the events array in order.
	 */
	protected function sortEvents() {
		usort($this->events, array($this, 'compareEvents'));
	}
	
	protected function compareEvents($a, $b) {
		$dateA = $a->Start;
		$dateB = $b->Start;
		if ($dateA < $dateB) {
			return -1;
		}
		elseif ($dateA == $dateB) {
			return 0;
		}
		return 1;
	}
}