<?php

namespace Application\View\Helper;

/** display a confirm dialog box before following a link */
class ConfirmDialog extends \Zend_View_Helper_Abstract
{
	public $view;

	public function __call($name, $arguments)
	{
		if( $name == "confirmDialog" )
			return call_user_func_array(array($this,"execute"), $arguments);
	}

	/**
	 * @param  $dialog_id         Id of the dialog div
	 * @param  $link_id           Id of the link div
	 * @param  $title             dialog title
	 * @param  $text              dialog text
	 * @param  $print_dialog_div  set false, if you provide your own dialog div
	 * @return string
	 */
    public function execute( $dialog_id, $link_id, $title, $text, $print_dialog_div)
    {
	    /* print dialog box (ZendX_JQuery) */
		$str = "";
	    $dialog_div = $this->view->dialogContainer($dialog_id,
							$text,
							array('draggable' => false,
								'modal' => true,
								'resizable'=>false,
								'title' => $title,
								'autoOpen' => false,
								'closeOnEscape' => true,
								'buttons'   => array(
									'Ok' =>  new \Zend_Json_Expr('function() {
										document.location = $("#'.$link_id.'").attr("href");
									}'),
									'Cancel' => new \Zend_Json_Expr('function() {
										$(this).dialog(\'close\');
									}')
								),
								));
		if( $print_dialog_div )
			$str .= $dialog_div;

	    /* interrupt onlick event */
	    $str .= '<script type="text/javascript">
				//<![CDATA[
					$( "#'.$link_id.'" )
						.click(function() {
							$( "#'.$dialog_id.'" ).dialog( "open" ); return false;
						});
				//]]>
				</script>';
	    
	    return $str;
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}