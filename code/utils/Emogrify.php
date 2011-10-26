<?php
/**
 * Simple cli controller used to create an css-inlined version of an html document.
 * @usage script/sake.sh SSTools_Utils_Emogrify --htmlfile=<filename> (--cssfile=<filename)
 * @author sergeim
 *
 */
class SSTools_Utils_Emogrify extends SSTools_Utils_CommandLineController {
	
	protected $argDefinitions = array(
		'htmlfile' => true, 'cssfile', 'outputfile'
	);
	
	function process() {
		$emogrifier = new Emogrifier(file_get_contents($this->argValue('htmlfile')));
		if ($cssfile = $this->argValue('cssfile')) {
			$emogrifier->setCSS(file_get_contents($cssfile));
		}
		$result = $emogrifier->emogrify();
		if ($outputfile = $this->argValue('outputfile')) {
			file_put_contents($outputfile, $result);
		}
		else {
			print ($result);
		}
	}
}