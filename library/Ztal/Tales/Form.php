<?php
/**
 * PHPTal Tale Modifiers.
 *
 * A collection of extensions to PHPTal that provide useful additional variable
 * handling routines within a form.
 *
 * @category  Namesco
 * @package   Central
 * @author    Alex Mace <amace@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Form Tale Modifiers.
 *
 * Creates a namespace for the tales extensions by clustering them as static
 * methods on the class. This class should never be subclassed. It is simply a
 * container for the various tales routines.
 *
 * @category Namesco
 * @package  Central
 * @author   Alex Mace <amace@names.co.uk>
 */
	
final class Ztal_Tales_Form implements PHPTAL_Tales
{

	/**
	 * Figures out the simple element type from the one passed in, which may be a full class name of an element.
	 *
	 * @param string $type The type to check.
	 *
	 * @return string
	 */
	public static function calculateType($type)
	{
		//this is done in two steps using the $nameParts intermediate variable
		//because it causes a strict error if something other than a defined variable
		//reference is passed to array_pop
		$nameParts = explode("_", $type);
		$type = strtolower(array_pop($nameParts));

		// Switch multicheckbox to be just "checkbox".
		if ($type == 'multicheckbox') {
			$type = 'checkbox';
		}
		
		return $type;

	}

	/**
	 * Gets the specified attribute from a form element based on the name.
	 *
	 * Example used within template:
	 * <input tal:attributes="maxlength Ztal_Tales_Form.getAttrib:element,string:maxlength" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function getAttrib($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(' . phptal_tale($a, $nothrow) . '->getAttrib(' 
			. phptal_tale($b, $nothrow) . ') != null ? '
			. phptal_tale($a, $nothrow) . '->getAttrib('
			. phptal_tale($b, $nothrow) . ') : '
			. phptal_tale($rest, $nothrow) . ')';
	}

	/**
	 * Gets the element from a form based on the name.
	 *
	 * Example used within template:
	 * <tal:block tal:define="element Ztal_Tales_Form.getElement:element,string:name" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function getElement($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(' . phptal_tale($a, $nothrow) . '->getElement(' 
			. phptal_tale($b, $nothrow) . ') != null ? '
			. phptal_tale($a, $nothrow) . '->getElement('
			. phptal_tale($b, $nothrow) . ') : '
			. phptal_tale($rest, $nothrow) . ')';
	}


	/**
	 * Gets the errors for a named element.
	 *
	 * Example used within template:
	 * <tal:block tal:define="element Ztal_Tales_Form.getErrors:form,elementName" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function getErrors($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(count(' . phptal_tale($a, $nothrow) . '->getErrors(' 
			. phptal_tale($b, $nothrow) . ')) > 0 ? '
			. phptal_tale($a, $nothrow) . '->getErrors('
			. phptal_tale($b, $nothrow) . ') : '
			. phptal_tale($rest, $nothrow) . ')';
	}


	/**
	 * Tal extension to determine the input field type of a variable.
	 *
	 * Example use within template:
	 * <input tal:attributes="type Ztal_Tales_Form.inputType:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function inputType($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . '->getType())';
	}

	/**
	 * Tal extension to determine whether or not the current element is a display group.
	 *
	 * Example use within template:
	 * <fieldset tal:condition="Ztal_Tales_Form.isDisplayGroup:element">
	 * </fieldset>
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isDisplayGroup($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return phptal_tale($src, $nothrow)
			   . ' instanceof Zend_Form_DisplayGroup';
	}

	/**
	 * Is the current element a standard element?.
	 *
	 * Tal extension to determine whether or not the current element is a
	 * standard form element like input, select, etc.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isFormElement:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isFormElement($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return '((' . phptal_tale($src, $nothrow)
			. ' instanceof Zend_Form_Element) && (Ztal_Tales_Form::calculateType('
			. phptal_tale($src, $nothrow) . '->getType()) != "hidden"))';
	}

	/**
	 * Is the current element a hidden element?.
	 *
	 * Tal extension to determine whether or not the current element is a
	 * hidden form element.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isHiddenElement:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isHiddenElement($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return '((' . phptal_tale($src, $nothrow)
			. ' instanceof Zend_Form_Element) && (Ztal_Tales_Form::calculateType('
			. phptal_tale($src, $nothrow) . '->getType()) == "hidden"))';
	}


	/**
	 * Tal extension to determine whether or not the current element is a button input.
	 *
	 * Example use within template:
	 * <button tal:condition="Ztal_Tales_Form.isButton:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isButton($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'button'";

	}

	/**
	 * Tal extension to determine whether or not the current element is an image captcha input.
	 *
	 * Example use within template:
	 * <button tal:condition="Ztal_Tales_Form.isImageCaptcha:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isImageCaptcha($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return '(Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'captcha' && method_exists("
				. phptal_tale($src, $nothrow) . ', "getImgUrl"))';
	}


	/**
	 * Tal extension to determine whether or not the current element is a captcha input.
	 *
	 * Example use within template:
	 * <button tal:condition="Ztal_Tales_Form.isCaptcha:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isCaptcha($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return '(Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'captcha' && !method_exists("
				. phptal_tale($src, $nothrow) . ', "getImgUrl"))';
	}



	/**
	 * Should the current option in a multi check box be checked or not.
	 *
	 * Determines if the current option for a multi check box should be checked
	 * or not. If the value of this option appears in the values of the element
	 * (rather than options, options are potential values) then it should be
	 * checked. First argument to the tale is the element and second is the
	 * current option.
	 *
	 * Example used within template:
	 * <tal:block tal:define="checked Ztal_Tales_Form.isChecked:element,repeat/option/key" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isChecked($src, $nothrow)
	{
		$break = strpos($src, ',');
		$a = substr($src, 0, $break);
		$src = substr($src, $break + 1);
		$break = strpos($src, '|');
		if ($break === false) {
			$b = $src;
			$rest = 'NULL';
		} else {
			$b = substr($src, 0, $break);
			$rest = substr($src, $break + 1);
		}
		return '(is_array(' . phptal_tale($a, $nothrow) . '->getValue()) ? ' .
			   'in_array(' . phptal_tale($b, $nothrow) . ', ' .
			   phptal_tale($a, $nothrow) . '->getValue()) : false)';
	}

	/**
	 * Tal extension to determine whether or not the current element is an input.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isInput:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isInput($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'in_array(Ztal_Tales_Form::calculateType('
			   . phptal_tale($src, $nothrow) . "->getType()), "
			   . "array('text', 'hidden', 'password', 'date', 'email'))";

	}

	/**
	 * Tal extension to determine whether or not the current element is a multi checkbox.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isMultiCheckbox:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isMultiCheckbox($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		// If an input has multiple options, then it will have the function
		// "getMultiOptions". However, selects also have that function so we
		// have to check that this isn't a select.
		return 'method_exists(' . phptal_tale($src, $nothrow)
			   . ", 'getMultiOptions') && Ztal_Tales_Form::calculateType("
			   . phptal_tale($src, $nothrow) . "->getType()) == 'checkbox'";

	}

	/**
	 * Tal extension to determine whether or not the current element is a radio element.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isRadio:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isRadio($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'radio'";

	}


	/**
	 * Tal extension to determine whether or not the current element is a checkbox.
	 *
	 * Example use within template:
	 * <input tal:condition="Ztal_Tales_Form.isCheckbox:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isCheckbox($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'checkbox'";

	}


	/**
	 * Tal extension to determine whether or not the current element is a select.
	 *
	 * Example use within template:
	 * <select tal:condition="Ztal_Tales_Form.isSelect:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isSelect($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'select'";
	}

	/**
	 * Tal extension to determine whether or not the current element is a textarea input.
	 *
	 * Example use within template:
	 * <textarea tal:condition="Ztal_Tales_Form.isTextarea:element" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function isTextarea($src, $nothrow)
	{

		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}

		return 'Ztal_Tales_Form::calculateType(' . phptal_tale($src, $nothrow)
			   . "->getType()) == 'textarea'";

	}

	/**
	 * Tal extension to determine whether or not the current element should have a label displayed before it.
	 *
	 * Example use within template:
	 * <label tal:condition="Ztal_Tales_Form.showLabelBefore:element"
	 *  	  i18n:translate="element/getLabel" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function showLabelBefore($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return 'in_array(Ztal_Tales_Form::calculateType('
			   . phptal_tale($src, $nothrow) . '->getType()), '
			   . "array('date', 'email', 'password', 'select', 'text', 'textarea')) && "
			   . phptal_tale($src, $nothrow) . '->getLabel()';
	}



	/**
	 * Tal extension to determine whether or not the current element should have a label displayed after it.
	 *
	 * Example use within template:
	 * <label tal:condition="Ztal_Tales_Form.showLabelAfter:element"
	 *  	  i18n:translate="element/getLabel" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function showLabelAfter($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break !== false) {
			$src = substr($src, 0, $break);
		}
		return 'in_array(Ztal_Tales_Form::calculateType('
			   . phptal_tale($src, $nothrow) . '->getType()), '
			   . "array('checkbox', 'radio')) && "
			   . phptal_tale($src, $nothrow) . '->getLabel()';
	}


	/**
	 * Tal extension to inject slot content into a variable.
	 *
	 * Slot names cannot (currently) be dynamic in phptal so this tale
	 * allows us to grab the content of a slot with a dynamic name and
	 * assign it to a variable which we can then output.
	 *
	 * Example use within template:
	 * <tal:block tal:define="slotContent Ztal_Tales_Form.getSlotContent:slotName" />
	 *
	 * @param string $src     The original template string.
	 * @param bool   $nothrow Whether to throw an exception on error.
	 *
	 * @return string
	 */
	public static function getSlotContent($src, $nothrow)
	{
		$break = strpos($src, '|');
		if ($break === false) {
			$slotName = $src;
			$notTrue = 'NULL';
		} else {
			$slotName = substr($src, 0, $break);
			$notTrue = substr($src, $break + 1);
		}
		return '($ctx->hasSlot(' . phptal_tale($slotName, $nothrow)
			. ')?$ctx->getSlot(' . phptal_tale($slotName, $nothrow)
			. '):' . phptal_tale($notTrue, $nothrow) . ')';
	}
	
}
