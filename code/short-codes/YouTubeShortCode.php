<?php

/**
 * Register this parser with the following code in your _config.php file:
 *
 *   ShortcodeParser::get()->register('YouTube', array('Page','YouTubeShortCodeHandler'));
 *
 * You can then insert a YouTube video into your content using:
 *
 *   [YouTube id=3UTu6lV8ppY]
 *
 * or
 *
 *   [YouTube id=3UTu6lV8ppY]This is the caption[/YouTube]
 */
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