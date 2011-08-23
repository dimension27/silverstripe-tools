<?php
class SSTools_Scheduling_ICalExporter extends SSTools_Scheduling_Exporter {
	
	/**
	 * Sets the host to use in the exported file.
	 * @var string
	 */
	protected $Host = null;
	
	/**
	 * Sets the prodid to use in the exported file.
	 * @var string
	 */
	protected $Prodid = null;
	
	/**
	 * Called before exporting a Schedule.
	 */
	protected function setup() {
		parent::setup();
		if (is_null($this->Host)) {
			$this->Host = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : 'dimension27.com';
		}
		if (is_null($this->Prodid)) {
			$this->Prodid = '-//'.$this->Host.'//NONSGML v1.0//EN';
		}
		$this->line('BEGIN:VCALENDAR');
		$this->line('VERSION:2.0');
		$this->line("PRODID:{$this->Prodid}");
	}
	
	protected function line($line) {
		$this->exported .= $line."\r\n";
	}
	
	protected function uid() {
		return md5(uniqid(mt_rand(), true)).'@'.$this->Host;
	}
	
	protected function datestamp($date = null) {
		$timestamp = null;
		if ($date) {
			$timestamp = $date->getTimestamp();
		}
		else {
			$timestamp = time();
		}
		return gmdate('Ymd\THis\Z', $timestamp);
		
	}
	
	public function setProdid($Prodid) {
		$this->Prodid = $Prodid;
	}
	
	/**
	 * Exports the given $Schedule as a configured SS_HTTPResponse instance, including correct headers, etc.
	 * @param SSTools_Scheduling_Schedule $Schedule
	 * @return SS_HTTPResponse The exported content.
	 */
	public function createResponse(SSTools_Scheduling_Schedule  $schedule) {
		$response = new SS_HTTPResponse($this->export($schedule));
		$response->addHeader('Content-type', 'text/calendar; charset=utf-8');
		$response->addHeader('Content-Disposition', 'inline; filename="calendar.ics"');
		return $response;
	}
	
	/**
	 * Exports the given $event. Should be overriden in sub-classes that export different formats.
	 * @param SSTools_Scheduling_Event $event
	 */
	protected function exportEvent(SSTools_Scheduling_Event $event){
		$this->line('BEGIN:VEVENT');
		$this->line("UID:{$this->uid()}");
		$this->line("DTSTAMP:{$this->datestamp()}");
		$this->line("DTSTART:{$this->datestamp($event->Start)}");
		$this->line("DTEND:{$this->datestamp($event->End)}");
		$this->line("SUMMARY:{$this->renderEventContent($event)}");
		$this->line('END:VEVENT');
	}
	
	/**
	 * Called after exporting all events in a Schedule.
	 */
	protected function teardown() {
		$this->line('END:VCALENDAR');
	}
}
