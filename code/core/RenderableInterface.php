<?php
/**
 * Defines the interface that must be implemented for an object to be used directly in a template.
 * Which doesn't actually seem to be defined anywhere.
 * @author sergeim
 * @nb
 *
 */
interface SSTools_Core_RenderableInterface {
	
	/**
	 * Return a rendered version of this renderable object.
	 * 
	 * This is returned when you access a renderable object as $Thingy rather
	 * than <% control Thingy %>
	 */
	function forTemplate();
}