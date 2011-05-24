<?php
class Utils {
	public static function ThemeDir($subtheme = false) {
		if( $theme = SSViewer::current_theme() ) return THEMES_DIR . "/$theme" . ($subtheme ? "_$subtheme" : null);
		return project();
	}
}
?>