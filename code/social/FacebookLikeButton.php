<?php
/**
 * Adds a facebook like button to a template. Requires that the page has been initialised using 
 * SSTools_Social_FacebookInit, if using the JS SDK.
 * @author sergeim
 *
 */
class SSTools_Social_FacebookLikeButton extends Object implements SSTools_Core_RenderableInterface {
	
	/**
	 * The app id. If an app id specified, then the JS SDK is used. Otherwise the iframe implementation is used.
	 * @var string
	 */
	protected $appId = null;
	
	/**
	 * The url to like. Defaults to the current page.
	 * @var string
	 */
	public $URL = null;
	
	/**
	 * The color scheme for the button. 
	 * @var string
	 */
	const LIGHT = 'light';
	const DARK = 'dark';
	public $ColorScheme = SSTools_Social_FacebookLikeButton::LIGHT;
	
	/**
	 * The verb to display. 'like' or 'recommend'
	 * @var string
	 */
	const LIKE = 'like';
	const RECOMMEND = 'recommend';
	public $Verb = SSTools_Social_FacebookLikeButton::LIKE;
	
	/**
	 * The width of the plugin, in pixels.
	 * @var integer
	 */
	public $Width = 450;
	
	/**
	 * The height of the plugin, in pixels.
	 * @var integer
	 */
	public $Height = 80;
	
	/**
	 * The layout style.
	 * @var string
	 */
	const STANDARD = 'standard';
	const BUTTON_COUNT = 'button_count';
	const BOX_COUNT = 'box_count';
	public $Layout = SSTools_Social_FacebookLikeButton::STANDARD;
	
	/**
	 * Whether or not to show profile pictures below the button.
	 * @var boolean
	 */
	public $ShowFaces = true;
	
	/**
	 * Whether or not to include a send button.
	 * @var boolean
	 */
	public $IncludeSend = true;
	
	public function __construct($appId = null) {
		$this->appId = $appId;
		parent::__construct();
	}
	
	protected function getConfig() {
		$config = array();
		foreach (array(
		'URL' => 'href',
		'ShowFaces' => 'show-faces',
		'IncludeSend' => 'send',
		'ColorScheme' => 'colorscheme',
		'Width' => 'width',
		'Layout' => 'layout',
		) as $property => $name) {
			$value = $this->$property;
			if (!is_null($value)) {
				if (is_bool($value)) {
					$value = $value ? 'true' : 'false';
				}
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
		return ($this->appId) ? $this->asXFBML() : $this->asIframe();
	}
	
	protected function asXFBML() {
		$dataAttributesString = '';
		foreach ($this->getConfig() as $name => $value) {
			$dataAttributesString .= ' data-'.$name.'="'.Convert::raw2att($value).'"';
		}
		return <<<EOS
<script>(function(d){
  var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#appId={$this->appId}&xfbml=1";
  d.getElementsByTagName('head')[0].appendChild(js);
}(document));</script>
<div class="fb-like"{$dataAttributesString}></div>
EOS;
	}
	
	protected function asIframe() {
		$parts = array();
		foreach ($this->getConfig() as $name => $value) {
			$parts[] = $name.'='.urlencode($value);
		}
		if (!$this->URL) {
			$parts[] = 'href='.urlencode(Utils::currentURL());
		}
		$queryString = implode('&', $parts);
		return <<<EOS
<iframe src="http://www.facebook.com/plugins/like.php?$queryString"
 scrolling="no" frameborder="0"
 style="border:none; width:{$this->Width}px; height:{$this->Height}px"></iframe>
EOS;
	}
}