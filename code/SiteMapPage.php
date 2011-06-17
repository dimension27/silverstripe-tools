<?php 

class SiteMapPage extends Page {

}

class SiteMapPage_Controller extends Page_Controller {

	function Content() {
		$rv = $this->getField('Content');
		$top = $this->Menu(1);
		$rv .= $this->getList($top);
		return $rv;
	}

	function getList( DataObjectSet $nodes ) {
		$rv = "<ul>\n";
		foreach( $nodes as $node ) { /* @var $node SiteTree */
			$rv .= "<li><a href='".$node->Link()."'>$node->Title</a>";
			if( $children = $node->Children("ShowInMenus = 1") ) {
				$rv .= $this->getList($children);
			}
		}
		$rv .= "</ul>";
		return $rv;
	}

}

?>