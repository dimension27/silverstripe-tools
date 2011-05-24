<?php

/**
 * A controller that allows the routing of restricted files to controllers for authorisation.
 * 
 * @author Alex Hayes <alex.hayes@dimension27.com>
 */
class RestrictedFile_Controller extends Page_Controller {

	/**
	 * An array of routes. 
	 * @var array
	 */
	public static $routes = array();

	/**
	 * Add a route.
	 * 
	 * <h2>Routes</h2>
	 * 
	 * $routes are regular expressions which, when matched, $callback will be called. Note the callback
	 * should be a extension of ContentController however it does not have to be. The callback should accept
	 * a single variable of type SS_HTTPRequest    
	 * 
	 * Example:
	 * 
	 * <code>
	 * // public/assets/.htaccess
	 * RewriteEngine On
	 * RewriteRule ^([private].*)$ /restricted-file/negotiate?route=assets/$1 [NC]
	 * 
	 * // mysite/_config.php
	 * Director::addRules(100, array(
	 *     'restricted-file' => 'RestrictedFile_Controller',
	 *     'my-controller' => 'MyController_Controller' // usually this would be an existing controller
	 * ));
	 * RestrictedFile_Controller::addRoute('/(?P<Filename>assets\/private\/.*)/', 'my-controller/download');
	 * 
	 * // mysite/code/MyDownload.php
	 * class MyController_Controller extends Page_Controller {
	 *     function download( SS_HTTPRequest $request ) {
	 *         $file = DataObject::get('File', "`Filename` = '" . Convert::raw2sql($request->getVar('Filename')) . "'");
	 *         
	 *         // perform authorisation check on $file object...
	 *         
	 *         return $request->send_file(
	 *             file_get_contents($file->getFullPath()),
	 *             basename($file->getFilename()) 
	 *         );
	 *     }
	 * }
	 * </code>
	 * 
	 *     1. A request comes in for uri 'assets/private/myfile.pdf'
	 *     2. Apache mod_rewrite intercepts the request and sends it to '/restricted-file/negotiate?route=assets/private/myfile.pdf'
	 *     3. SilverStripe processes the request and hands it onto RestrictedFile_Controller::negotiate
	 *     4. RestrictedFile_Controller::negotiate checks to see that there is a re-route and forwards the request on.
	 *     4. MyController_Controller::download is called with an instance of SS_HTTPRequest
	 *     5. MyController_Controller::download performs some kind of authorisation on the File object
	 *     6. File is sent to client
	 * 
	 * Note that you could do all this with mod_rewrite if your heart so desired and bypass the need
	 * for this class.
	 * 
	 * @param string $pattern    A pattern that will be parsed by preg_match.
	 * @param string $url        A callback that can be called by call_user_func_array.
	 *
	 * @see http://php.net/callback
	 * @author Alex Hayes <alex.hayes@dimension27.com>
	 */
	public static function addRoute($pattern, $url) {
		self::$routes[$pattern] = $url;
	}

	/**
	 * Negotiate the request and either call a callback or send a 404.
	 * 
	 * @param SS_HTTPRequest $request
	 * @return false|callback              If a valid route was matched, a callback is returned, 
	 *                                     otherwise false is returned.
	 * @see http://php.net/callback
	 * @author Alex Hayes <alex.hayes@dimension27.com>
	 */
	public function negotiate( SS_HTTPRequest &$request ) {
		$subject = $request->getVar('route');
		foreach( self::$routes as $pattern => $url ) {
			if( preg_match($pattern, $subject, $matches) ) {
				$getVars = $request->getVars();
				foreach($matches as $key => $value) {
					if( !is_numeric($key) ) {
						// Only add non-numeric (ie.. named) matches to the request.
						$_GET[$key] = $value;
					}
				}
				return Director::direct($url);
			}
		}
		trigger_error(__METHOD__ . ' no match for route: ' . $request->getVar('route'), E_USER_WARNING);
		$this->httpError(404);
	}
	
}