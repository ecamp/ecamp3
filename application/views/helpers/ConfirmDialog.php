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

    public function execute( $dialog_id, $link_id, $title, $text)
    {
	    /* print dialog box (ZendX_JQuery) */
	    $str = $this->view->dialogContainer($dialog_id,
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

	    /* interrupt onlick event */
	    $str .= '<script>
					$( "#'.$link_id.'" )
						.click(function() {
							$( "#'.$dialog_id.'" ).dialog( "open" ); return false;
						});
				</script>';
	    
	    return $str;
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}