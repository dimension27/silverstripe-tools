<?php
/**
 * Simple cli controller used to create an css-inlined version of an html document.
 * @usage script/sake.sh SSTools_Utils_Emogrify --htmlfile=<filename> (--cssfile=<filename)
 * @author sergeim
 *
 */
class SSTools_Utils_Emogrify extends CliController {
	
	protected $argDefinitions = array(
		'htmlfile' => true, 'cssfile', 'outputfile'
	);
	
	protected $htmlfile = null;
	protected $cssfile = null;
	protected $outputfile = null;
	
	function index() {
		foreach(ClassInfo::subclassesFor($this->class) as $subclass) {
			$task = new $subclass();
			$task->init();
			$task->process();
		}
	}
	
	function process() {
		try {
			$this->initialiseArgs();
			$emogrifier = new Emogrifier(file_get_contents($this->htmlfile));
			if ($this->cssfile) {
				$emogrifier->setCSS(file_get_contents($this->cssfile));
			}
			$result = $emogrifier->emogrify();
			if ($this->outputfile) {
				file_put_contents($this->outputfile, $result);
			}
			else {
				print ($result);
			}
		}
		catch (Exception $e) {
			$this->usage($e->getMessage());
		}
	}
	
	function initialiseArgs() {
		foreach ($this->argDefinitions as $name => $value) {
			$required = true;
			if (is_numeric($name)) {
				$name = $value;
				$required = false;
			}
			if (isset($_GET[$name])) {
				$this->$name = $_GET[$name];
			}
			elseif ($required) {
				throw new Exception("'$name' must be specified");
			}
		}
	}
	
	protected function usage($message = '') {
		$message .= "\nUsage: script/sake.sh ".$this->class;
		foreach ($this->argDefinitions as $name => $definition) {
			$required = true;
			if (is_numeric($name)) {
				$name = $definition;
				$required = false;
			}
			$part = '--'.$name.'=<'.$name.'>';
			if (!$required) {
				$part = "($part)";
			}
			$message .= ' '.$part;
		}
		print($message);
		exit(1);
		
	}
}