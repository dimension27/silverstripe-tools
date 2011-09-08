<?php
/**
 * Adds a tweet button to a template. 
 * @author sergeim
 *
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
	 * The layout of the count
	 * @var string
	 */
	const VERTICAL = 'vertical';
	const HORIZONTAL = 'horizontal';
	const NONE = 'none';
	public $CountLayout = self::VERTICAL;
	
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