<?php
class Utils {
	public static function ThemeDir( $subtheme = false ) {
		if( $theme = SSViewer::current_theme() )
			return THEMES_DIR . "/$theme" . ($subtheme ? "_$subtheme" : null);
		return project();
	}
	
	public static function GetURIFromID( $id ) {
		$sitetree = DataObject::get_one('SiteTree', "\"SiteTree\".ID = '$id'");
		if( is_object($sitetree) )
			return $sitetree->RelativeLink();
		return '';
	}
}
?>