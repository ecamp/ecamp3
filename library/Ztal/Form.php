<?php
/**
 * Ztal Form.
 *
 * @category  Namesco
 * @package   Ztal
 * @author    Alex Mace <amace@names.co.uk>
 * @copyright 2009-2011 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Ztal Form.
 *
 * This makes a few customisations to Zend_Form for use with Ztal applications.
 *
 * @category Namesco
 * @package  Ztal
 * @author   Alex Mace <amace@names.co.uk>
 */
class Ztal_Form extends Zend_Form
{

	/**
	 * Constructor.
	 *
	 * Turns off Zend_Form translation before calling the parent constructor.
	 *
	 * @param mixed $options Set of options for configuring the form.
	 */
	public function __construct($options = null)
	{
		// Turn off the translator as this will be done in PHPTAL.
		$this->setDisableTranslator(true);

		// Call the parent constructor to set up everything else.
		parent::__construct($options);

	}

	/**
     * Create an element.
     *
     * Acts as a factory for creating elements. Elements created with this
     * method will not be attached to the form, but will contain element
     * settings as specified in the form object (including plugin loader
     * prefix paths, default decorators, etc.).
	  *
	  * This extends the version in Zend_Form and turns off translation
	  * and decorators by default.
     *
     * @param string            $type    Form element type.
     * @param string            $name    Name of the element.
     * @param array|Zend_Config $options Set of configuration options.
	 *
     * @return Zend_Form_Element
	 */
	public function createElement($type, $name, $options = null)
	{
		// Turn off the translator by default because we'll be using PHPTAL.
		if (!isset($options['disableTranslator'])) {
			$options['disableTranslator'] = true;
		}

		// Turn off loading any decorators because they are unneeded and we use
		// decorators to specify what TAL macro to use.
		$options['disableLoadDefaultDecorators'] = true;

		return parent::createElement($type, $name, $options);
	}
}