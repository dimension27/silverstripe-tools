<?php

class YouTubeShortCode {

	public static $defaults = array(
			'autoplay' => false,
			'loop' => false,
			'width' => 640,
			'height' => 385,
	);

	function handleShortcode( $arguments, $enclosedContent = null, $parser = null ) {
		$defaults = array(
					'YouTubeID' => preg_replace('!.*/!', '', $arguments['id']),
					'caption' => $enclosedContent ? Convert::raw2xml($enclosedContent) : false,
		);
		$template = new SSViewer('YouTubeShortCode');
		return $template->process(new ArrayData(array_merge(self::$defaults, $defaults, $arguments)));
	}

}

?>