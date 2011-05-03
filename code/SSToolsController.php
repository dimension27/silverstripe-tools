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

}

?>