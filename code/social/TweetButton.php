<?php
/**
 * Adds a tweet button to a template. 
 * See https://dev.twitter.com/docs/tweet-button for details of this API.
 * @author sergeim
 */
class SSTools_Social_TweetButton extends Object implements SSTools_Core_RenderableInterface {
	
	/**
	 * The url to tweet. Defaults to the current page.
	 * @var string
	 */
	public $URL = null;

	/**
	 * The account to tweet. 
	 * @var string
	 */
	public $Account = null;

	/**
	 * Controls the layout of the count. Options are: none, horizontal, vertical
	 * @var string
	 */
	public $CountLayout = self::VERTICAL;

	const VERTICAL = 'vertical';
	const HORIZONTAL = 'horizontal';
	const NONE = 'none';
	
	/**
	 * The text that people will include in their Tweet when they share from your website.
	 * Defaults to the title of the page.
	 * @var string
	 */
	public $Text = null;
	
	protected function getConfig() {
		$config = array();
		foreach (array(
		'URL' => 'url',
		'Account' => 'via',
		'CountLayout' => 'count',
		'Text' => 'text',
		) as $property => $name) {
			$value = $this->$property;
			if (!is_null($value)) {
				$config[$name] = $value;
			}
		}
		return $config;
	}
	
	/**
	 * Return a rendered version of this renderable object.
	 * 
	 * This is returned when you access a renderable object as $Thingy rather
	 * than <% control Thingy %>
	 */
	function forTemplate(){
		$dataAttributes = '';
		foreach ($this->getConfig() as $name => $value) {
			$dataAttributes .=  ' data-'.$name.'="'.Convert::raw2att($value).'"';
		}
		return <<<EOS
<a href="http://twitter.com/share" class="twitter-share-button"{$dataAttributes}>Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
EOS;
	}

}
