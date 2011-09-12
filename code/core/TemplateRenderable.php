<?php
/**
 * Provides the ability for sub-classes to be rendered directly inside a template, using a template of their own.
 * @author sergeim
 *
 */
class SSTools_Core_TemplateRenderable extends ViewableData implements SSTools_Core_RenderableInterface {
	
	/**
	 * The SS template to render this renderable object HTML into.
	 * Default is ClassName, but this can be changed to
	 * another template for customisation.
	 * 
	 * @see SSTools_Core_Renderable->setTemplate()
	 * @var string
	 */
	protected $template;
	
	/**
	 * Set the SS template that this renderable object should use
	 * to render with.
	 * 
	 * @param string $template The name of the template (without the .ss extension)
	 */
	function setTemplate($template) {
		$this->template = $template;
	}
	
	/**
	 * Return the template to render this renderable object with.
	 * If the template isn't set, then default to the
	 * renderable object class name e.g "Thingy".
	 * 
	 * @return string
	 */
	function getTemplate() {
		if($this->template) return $this->template;
		else return $this->class;
	}
	
	/**
	 * Return a rendered version of this renderable object.
	 * 
	 * This is returned when you access a renderable object as $Thingy rather
	 * than <% control Thingy %>
	 */
	function forTemplate(){
		return $this->renderWith($this->getTemplate());
	}
}