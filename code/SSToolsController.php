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
			$workingFolder = $baseFolder.'/silverstripe-cache';
			chdir($workingFolder);
			$outputFile = preg_replace('!(http://|/)!', '', $url).'/wget-output.log';
			if( !is_dir($dir = dirname($outputFile)) ) {
				mkdir($dir);
			}
			$command = "wget -o $outputFile --execute robots=off --recursive --page-requisites '$url'";
			echo "Crawling $url (output saved to $workingFolder)...\n";
			// If you're impatient, you can run:
			// tail -f public/sapphire/wget-output.log | egrep 'awaiting response...' | egrep -v '(200 OK|302 Found)'
			flush();
			/* debug */ echo $command.NL;
			echo `$command`;
			$output = `cat $outputFile | egrep 'awaiting response...' | egrep -v '(200 OK|302 (Found|OK))'`;
			echo ($output
					? "Some errors were found, see $workingFolder/$outputFile for details".NL.$output
					: 'No errors found').NL;
		}
	}

	public function crawl_sites( SS_HTTPRequest $request ) {
		if( class_exists('Subsite') ) {
			foreach( DataObject::get('Subsite', 'IsPublic = 1') as $subsite ) { /* @var $subsite Subsite */
				self::crawl_site($request, $subsite->absoluteBaseURL());
			}
		}
	}

	public function publish_all( SS_HTTPRequest $request ) {
		if( class_exists('Subsite') ) {
			$total = 0;
			foreach( DataObject::get('Subsite', 'IsPublic = 1') as $subsite ) { /* @var $subsite Subsite */
				echo "Publishing '$subsite->Title'".NL;
				Subsite::changeSubsite($subsite);
				$total += $this->publishSite($subsite);
			}
			echo "Published $total pages".NL;
		}
		else {
			$this->publishSite();
		}
		$this->extend('publishAll');
	}

	function publishSite( $subsite = null ) {
		$limit = 100;
		$offset = 0;
		$count = 0;
		$member = self::getAdminMember();
		do {
			if( $pages = DataObject::get("SiteTree", "", "", "", "$offset,$limit") ) {
				$offset += $limit;
				foreach( $pages as $page ) { /* @var $page SiteTree */
					if( !$page || !$page->canPublish($member) ) {
						echo "\tCan't publish ".$page->Link().NL;
					}
					else {
						echo "\tPublished ".$page->Link().NL;
					}
					$page->doPublish();
					$page->destroy();
					unset($page);
					$count++;
				}
			}
		}
		while( $pages && ($pages->Count() > 0) );
		echo "\tPublished $count pages".NL;
		$this->extend('publishSite', $subsite);
		return $count;
	}

	static function getAdminMember() {
		static $member;
		if( !isset($member) ) {
			$group = Group::get_one('Group', 'Code = \'administrators\'');
			if( !$member = $group->Members()->First() ) {
				trigger_error("Couldn't find any Members of Group 'administrators'", E_USER_ERROR);
			}
		}
		return $member;
	}

}

?>