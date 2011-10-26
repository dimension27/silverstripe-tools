<?php
/**
 * Simple cli controller used to run arbitrary code.
 * @usage script/sake.sh SSTools_Utils_CodeRunner --htmlfile=<filename> (--cssfile=<filename)
 * @author sergeim
 *
 */
class SSTools_Utils_CodeRunner extends SSTools_Utils_CommandLineController {
	
	protected $argDefinitions = array(
		'code',
		'file'
	);
	
	function process() {
		$code = $this->argValue('code');
		$file = $this->argValue('file');
		if (!($code xor $file)) {
			throw new Exception("Either code or file (but not both) must be specified.");
		}
		if ($code) {
			eval($code);
		}
		else  {
			include($file);
		}
	}
	
}