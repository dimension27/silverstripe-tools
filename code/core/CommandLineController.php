<?php
/**
 * Simple cli controller sub-class that doesn't run all its subclasses when asked to run itsef, 
 * and contains better support for arguments.
 * @author sergeim
 *
 */
class SSTools_Utils_CommandLineController extends CliController {
	
	protected $args = array();
	
	protected $argDefinitions = array();
	
	function init() {
		parent::init();
		try {
			$this->initialiseArgs();
		}
		catch (Exception $exception){
			$this->usage($exception->getMessage());
		}
	}
	
	function index() {
		try {
			$this->process();
		}
		catch (Exception $exception){
			$this->usage($exception->getMessage(), $exception);
		}
	}
	
	function initialiseArgs() {
		foreach ($this->getArgDefinitions() as $name => $value) {
			$required = false;
			$default = null;
			if (is_numeric($name)) {
				$name = $value;
				$required = false;
			}
			elseif (is_array($value)) {
				$required = $this->arrayValue($value, 'required', false);
				$default = $this->arrayValue($value, 'default');
			}
			else {
				$required = $value;
			}
			
			$this->args[$name] = $default;
			if (isset($_GET[$name])) {
				$this->args[$name] = $_GET[$name];
			}
			elseif ($required) {
				throw new Exception("'$name' must be specified");
			}
		}
	}
	
	protected function getArgDefinitions() {
		return $this->argDefinitions;
	}
	
	/**
	 * Returns the value of the argument named $name
	 * @param string $name
	 */
	protected function argValue($name) {
		if (!array_key_exists($name, $this->args)) {
			throw new Exception("Unknown argument '$name'");
		}
		return $this->args[$name];
	}
	
	/**
	 * Returns the value for the given $key in the given $array. If not set, returns the given $default.
	 * @param array $array
	 * @param mixed $key
	 * @param mixed $default
	 */
	public function arrayValue(array $array, $key, $default = null) {
		if (isset($array[$key])) {
			return $array[$key];
		}
		return $default;
	}
	
	/**
	 * Shows a usage message, optionally with an exception, and exits.
	 * @param string $message
	 * @param Exception $exception
	 * @TODO
	 */
	protected function usage($message = null, Exception $exception = null) {
		if (is_null($message)) {
			$message = '';
		}
		$message .= "\nUsage: script/sake.sh ".$this->class;
		//TODO: refactor and update arg definition display to handle defaults, etc.
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
		print($message."\n");
		if ($exception) {
			throw $exception;
		}
		exit(1);
	}
}