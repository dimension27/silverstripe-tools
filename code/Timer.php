<?php

require_once "Benchmark/Timer.php";

class SS_Benchmark_Timer {
	
	/**
	 * @var Benchmark_Timer
	 */
	static $timer;
	
	static public function init() {
		self::$timer = new Benchmark_Timer();
	}
	
	static public function start() {
		self::$timer->start();
	}
	
	static public function setMarker($name) {
		self::$timer->setMarker($name);
	}

	static public function getOutput($showTotal = false, $format = 'plain') {
		return self::$timer->getOutput($showTotal, $format);
	}
	
}