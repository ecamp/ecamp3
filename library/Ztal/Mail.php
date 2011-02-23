<?php
/**
 * Ztal Mail.
 *
 * @category  Namesco
 * @package   Ztal_Form
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Ztal Mail.
 *
 * Provides template support for emails.
 *
 * @category Namesco
 * @package  Ztal_Mail
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class Ztal_Mail extends Zend_Mail
{
	/**
	 * The view object used to render the email content.
	 *
	 * @var Ztal_Tal_View
	 */
	public $view = null;
	
	
	/**
	 * Generate a macro launch stub to render the correct user template.
	 *
	 * Emails can have plain and html parts and these are held in a single
	 * template file by making them macros with names of 'plain' and 'html'.
	 * In order to render the correct macro, this src string is used.
	 *
	 * @return array
	 */
	protected function _template()
	{
		$src = '<tal:block
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:tal="http://xml.zope.org/namespaces/tal"
	xmlns:metal="http://xml.zope.org/namespaces/metal"
	xmlns:i18n="http://xml.zope.org/namespaces/i18n"
	xmlns:phptal="http://phptal.org/ns/phptal"
>
	<tal:block metal:use-macro="${ztalMailMacro}" />
</tal:block>';
		return array('src' => $src, 'name' => __FILE__);
	}
	
	
	/**
	 * Calculate the path for the email template.
	 *
	 * @param string $template The template name.
	 *
	 * @return string
	 */
	protected function _calculateTemplatePath($template)
	{
		$frontController = Zend_Controller_Front::getInstance();
		return 'emails/' . $template . '.email';
	}
	
	
	/**
	 * Constructor.
	 *
	 * @param string $charset The charset to set for the email content.
	 */
	public function __construct($charset = 'iso-8859-1')
	{
		if (!Zend_Registry::isRegistered('Ztal_View')) {
			throw new Exception('No available Ztal View');
		}
		
		$this->view = clone Zend_Registry::get('Ztal_View');
		$this->view->layout()->disableLayout();
		$this->view->setCompressWhitespace(true);

		
		parent::__construct($charset);
	}
	
	
	/**
	 * Set the plaintext body of the email to the output from the named template.
	 *
	 * @param string $template The name of the template.
	 * @param string $charset  The charset to use for the content.
	 * @param int    $encoding The encoding to use for the content.
	 *
	 * @return Ztal_Mail
	 */
	public function setBodyTextFromTemplate($template, $charset = null,
		$encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE
	) {
		$this->view->ztalMailMacro = $this->_calculateTemplatePath($template)
			. '/plain';
			
		return $this->setBodyText($this->view->render(
			$this->_template()), $charset, $encoding);
	}
	
	
	/**
	 * Set the html body of the email to the output from the named template.
	 *
	 * @param string $template The name of the template.
	 * @param string $charset  The charset to use for the content.
	 * @param int    $encoding The encoding to use for the content.
	 *
	 * @return Ztal_Mail
	 */
	public function setBodyHtmlFromTemplate($template, $charset = null,
		$encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE
	) {
		$this->view->ztalMailMacro = $this->_calculateTemplatePath($template)
			. '/html';

		return $this->setBodyHtml($this->view->render(
			$this->_template()), $charset, $encoding);
	}

}