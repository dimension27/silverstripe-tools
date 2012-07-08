<?php 

class NullMailer extends Mailer {

	function sendPlain($to, $from, $subject, $plainContent, $attachedFiles = false, $customHeaders = false) {
		return true;
	}
	
	function sendHTML($to, $from, $subject, $htmlContent, $attachedFiles = false, $customHeaders = false, $plainContent = false, $inlineImages = false) {
		return true;
	}

}

?>