<?php

class SSToolsController extends CliController {

	public function duplicate( SS_HTTPRequest $request ) {
		$class = $request->getVar('class');
		$id = $request->getVar('id');
		if( $class && $id ) {
			if( $obj = DataObject::get_by_id($class, $id) ) {
				$obj->duplicate();
			}
		}
		else {
			echo "USAGE: ".$request->getURL()." class={class} id={id} [name={name}]\n"
				."Duplicates the DataObject with the specified class and ID\n";
		}
	}

	public function crawl_site( SS_HTTPRequest $request, $url = null ) {
		$baseFolder = Director::baseFolder();
		if( !$url ) {
			$url = $request->getVar('url');
		}
		if( !$url ) {
			$url = $GLOBALS['_FILE_TO_URL_MAPPING'][$baseFolder];
		}
		if( $url ) {
			$outputFile = 'wget-output.log';
			$command = "wget -o $outputFile -e robots=off -r -p '$url'";
			echo "Crawling site '$url' using '$command'\n";
			// If you're impatient, you can run:
			// tail -f public/sapphire/wget-output.log | egrep 'awaiting response...' | egrep -v '(200 OK|302 Found)'
			flush();
			`$command`;
			`cat $outputFile | egrep 'awaiting response...' | egrep -v '(200 OK|302 Found)'`;
		}
	}

	public function crawl_sites( SS_HTTPRequest $request ) {
		if( class_exists('Subsite') ) {
			foreach( DataObject::get('Subsite', 'IsPublic = 1') as $subsite ) { /* @var $subsite Subsite */
				self::crawl_site($request, $subsite->absoluteBaseURL());
			}
		}
	}

}

?>