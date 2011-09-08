<?php
class Utils {

	public static function ThemeDir( $subtheme = false ) {
		if( $theme = SSViewer::current_theme() ) {
			return THEMES_DIR . "/$theme" . ($subtheme ? "_$subtheme" : null);
		}
		return project();
	}

	public static function BasePath() {
		return BASE_PATH;
	}

	public static function ProjectDir() {
		return BASE_PATH.'/'.project();
	}
	
	public static function GetURIFromID( $id ) {
		if( $siteTree = DataObject::get_by_id('SiteTree', $id) ) {
			return $siteTree->RelativeLink();
		}
	}

	public static function GetAnchorFromID( $id, $class = NULL ) {
		$sitetree = DataObject::get_one('SiteTree', "\"SiteTree\".ID = '$id'");
		if( is_object($sitetree) )
			return '<a href="' . $sitetree->RelativeLink() . '"' . ($class ? ' class="'.$class.'"' : '') . '>' . $sitetree->Title . '</a>';
		return '';
	}

	/**
	 * Returns a script tag containing the given $script
	 * @param script The script content
	 */
	public static function customScript( $script ) {
		$tag = "<script type=\"text/javascript\">\n//<![CDATA[\n";
		$tag .= "$script\n";
		$tag .= "\n//]]>\n</script>\n";
		return $tag;
	}

	/**
	 * Load the given javascript template with the page, returning the result.
	 * @param file The template file to load.
	 * @param vars The array of variables to load.  These variables are loaded via string search & replace.
	 */
	public static function javascriptTemplate($file, Array $vars = null) {
		$script = file_get_contents(Director::getAbsFile($file));
		$search = array();
		$replace = array();

		if($vars) foreach($vars as $k => $v) {
			$search[] = '$' . $k;
			$replace[] = str_replace("\\'","'", Convert::raw2js($v));
		}
		return self::customScript(str_replace($search, $replace, $script));
	}

	public static function createGroup( $code, $title, $description, $subsiteIds = null ) {
		if( class_exists('Subsite') ) {
			$oldState = Subsite::$disable_subsite_filter;
			Subsite::disable_subsite_filter();
		}
		if( !$group = DataObject::get_one('Group', "Code = '$code'") ) {
			$group = new Group();
			$group->Title = $title;
			$group->Description = $description;
			$group->Code = $code;
		}
		if( $subsiteIds ) {
			$group->AccessAllSubsites = false;
			// you have to write() before calling setByIDList(), because the write adds the current subsite
			$group->write();
			$group->Subsites()->setByIDList($subsiteIds);
		}
		else {
			$group->write();
		}
		if( class_exists('Subsite') ) {
			Subsite::disable_subsite_filter($oldState);
		}
	}
	
	/**
	 * Returns the full url to the current page.
	 */
	public static function currentURL() {
		$base = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$protocol = (empty($_SERVER['HTTPS'])) ? 'https' : 'http';
		return $protocol.'://'.$base;
	}
}

?>
