<?php
class SSTools_Scheduling_Event extEnds ViewableData {
	/**
	 * The Start of the event.
	 * @var DateTime
	 */
	protected $Start = null;
	
	/**
	 * The End of the event.
	 * @var DateTime
	 */
	protected $End = null;
	
	/**
	 * Arbitrary content for the event.
	 * @var mixed
	 */
	protected $content = null;
	
	public function __construct(DateTime $Start = null, DateTime $End = null, $content = null) {
		parent::__construct();
		$this->Start = $Start;
		$this->End = $End;
		$this->content = $content;
	}
	
	public function getEnd() {
		if (is_null($this->End)) {
			$this->End = clone($this->Start);
		}
		return $this->End;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getContent() {
		return $this->content;
	}
}