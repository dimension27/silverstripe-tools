<?php
/**
 * Used to initialise a page for use of the facebook javascript SDK. Must be included right after the body tag.
 * eg.
 * //In controller or page.
 * function getFacebookInit() {
 *   return new SSTools_Social_FacebookInit();
 * }
 * ...
 * //In template
 * <html>
 *   <body>
 *   $FacebookInit
 * ...
 * @author sergeim
 *
 */
class SSTools_Social_FacebookInit extends Object implements SSTools_Core_RenderableInterface {
	
	/**
	 * The app id for which to initialise the page.
	 * @var string
	 */
	protected static $app_id = null;
	
	/**
	 * Sets the app id.
	 * @param string $appId
	 */
	public static function set_app_id($appId){
		self::$app_id = $appId;
	}
	
	/**
	 * Gets the app id.
	 */
	public static function get_app_id(){
		$appId = self::$app_id;
		if (!$appId) {
			throw new SSTools_Social_FacebookException('No facebook app id has been set. Ensure app id is set with SSTools_Social_FacebookInit::set_app_id($appId)');
		}
		return $appId;
	}
	
	/**
	 * Factory method for creating JS SDK like buttons.
	 */
	public static function like_button() {
		return new SSTools_Social_FacebookLikeButton(self::get_app_id());
	}
	
	/**
	 * Return a rendered version of this renderable object.
	 * 
	 * This is returned when you access a renderable object as $Thingy rather
	 * than <% control Thingy %>
	 */
	function forTemplate(){
		$appId = $this->get_app_id();
		if ($host = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:null) {
			$channelUrlDefinition = "channelUrl : 'http://$host/channel.html', // channel.html file";
			
		}
		return 
		<<<EOS
		<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  FB.init({
    appId  : '$appId',
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true, // parse XFBML
    $channelUrlDefinition
    oauth  : true // enable OAuth 2.0
  });
</script>
EOS;
	}
}