<?php
/**
 * Abstract super class of all Schedule exporters.
 * @author sergeim
 *
 */
abstract class SSTools_Scheduling_Exporter extends ViewableData {
	
	/**
	 * A callback used to render the content of each event. 
	 * Should take one argument, which will be an instance of SSTools_Scheduling_Event
	 * @var callback
	 */
	protected $eventRenderer = null;
	
	/**
	 * The date format.
	 * @var string
	 */
	protected $dateFormat = 'j/n/Y';
	
	/**
	 * The Schedule currently being rendered.
	 * @var SSTools_Scheduling_Schedule
	 */
	protected $Schedule;
	
	/**
	 * The exported <whatever>. By default, treated as a string at this level.
	 * @var mixed
	 */
	protected $exported;
	
	public function __construct($eventRenderer = null) {
		parent::__construct();
		$this->eventRenderer = $eventRenderer;
	}
	
	/**
	 * Exports the given $Schedule in whatever format this exporter creates.
	 * @param SSTools_Scheduling_Schedule $Schedule
	 * @return mixed The exported content.
	 */
	public function export(SSTools_Scheduling_Schedule  $Schedule) {
		$this->Schedule = $Schedule;
		$this->setup();
		$this->doExport();
		$this->teardown();
		return $this->getExported();
	}
	
	/**
	 * Performs the actual export of the current Schedule.
	 */
	protected function doExport() {
		foreach ($this->Schedule->getEvents() as $event) {
			$this->exportEvent($event);
		}
	}
	
	protected function getExported() {
		return $this->exported;
	}
	
	/**
	 * Exports the given $event. Should be overriden in sub-classes that export different formats.
	 * @param SSTools_Scheduling_Event $event
	 */
	protected function exportEvent(SSTools_Scheduling_Event $event){
		$this->exported .= $this->renderEventDate($event).': '.$this->renderEventContent($event)."\n";
	}
	
	protected function renderEventDate(SSTools_Scheduling_Event $event){
		$start = $event->Start;
		$rv = $start->format($this->dateFormat);
		$end = $event->End;
		if ($end != $start) {
			$rv .= ' - '.$end->format($this->dateFormat);
		}
		return $rv;
	}
	
	protected function renderEventContent(SSTools_Scheduling_Event $event){
		if ($this->eventRenderer) {
			return call_user_func($this->eventRenderer, $event);
		}
		return $event->content();
	}
	
	/**
	 * Called before exporting a Schedule.
	 */
	protected function setup() {
		$this->exported = '';
	}
	
	/**
	 * Called after exporting all events in a Schedule.
	 */
	protected function teardown() {}
}
