<?php

class HelpField extends LiteralField {

	function FieldHolder() {
		return '<p class="note">'.(is_object($this->content) ? $this->content->forTemplate() : $this->content).'</p>';
	}

}

?>