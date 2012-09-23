<?php

/**
 * See http://nijikokun.github.com/bootstrap-notify/
 *
 * @author simonwade
 *
 */
class Flash {

	static $sessionKey = 'flash';
	static $elementSelector = '#flash';
	static $defaultOptions = array();

	protected static function &getStore() {
		if( !isset($_SESSION[self::$sessionKey]) ) {
			$_SESSION[self::$sessionKey] = array();
		}
		return $_SESSION[self::$sessionKey];
	}

	public static function addText( $type, $message, $options = array()  ) {
		return self::addHTML(htmlspecialchars($type), $message, $options);
	}

	public static function addHTML( $type, $message, $options = array() ) {
		$store =& self::getStore();
		$store[] = array(
			'type' => $type,
			'message' => $message,
			'options' => $options,
		);
	}

	public static function clear() {
		$store =& self::getStore();
		$store = array();
	}

	public static function hasMessages() {
		$store =& self::getStore();
		return sizeof($store) > 0;
	}

	public static function getBootstrapNotifyJS() {
		$store =& self::getStore();
		$rv = "jQuery(function($) {\n";
		foreach( $store as $message ) {
			$json = json_encode(array_merge(array(
					"message" => array("html" => $message['message']),
					"type" => $message['type'],
				), self::$defaultOptions, $message['options']
			));
			$rv .= "$('".self::$elementSelector."').notify($json).show();\n";
		}
		$rv .= "});";
		self::clear();
		return $rv;
	}

}
