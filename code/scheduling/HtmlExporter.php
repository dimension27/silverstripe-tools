<?php
class SSTools_Scheduling_HtmlExporter extends SSTools_Scheduling_Exporter {
	protected $count = 0;
	
	/**
	 * Called before exporting a Schedule.
	 */
	protected function setup() {
		parent::setup();
		$this->count = 0;
		$this->exported = 
<<<EOS
<div class="schedule">
	<h2>{$this->Schedule->getTitle(true, $this->dateFormat)}</h2>
EOS;
	}
	
	/**
	 * Called after exporting all events in a Schedule.
	 */
	protected function teardown() {
		$this->exported .= "\n</div>";
	}
	
	/**
	 * Exports the given $event. Should be overriden in sub-classes that export different formats.
	 * @param SSTools_Scheduling_Event $event
	 */
	protected function exportEvent(SSTools_Scheduling_Event $event){
		$oddEven = ($this->count % 2) ? 'odd' : 'even';
		$this->exported .= 
<<<EOS

	<div class="event $oddEven">
		<div class="event-date">
		{$this->renderEventDate($event)}
		</div>
		<div class="event-content">
		{$this->renderEventContent($event)}
		</div>
	</div>

EOS;
		$this->count++;
	}
}