<?php
/**
* @copyright Copyright (C) 2014 OSTraining.com
* @license GNU/GPL
*
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
* Vimeo Video Embedder Content Plugin
*
*/
class plgContentVimeoEmbed extends JPlugin
{

	/**
	* Constructor
	*
	* @param object $subject The object to observe
	* @param object $params The object that holds the plugin parameters
	*/
	function plgContentVimeoEmbed( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	* Example prepare content method
	*
	* Method is called by the view
	*
	* @param object The article object. Note $article->text is also available
	* @param object The article params
	* @param int The 'page' number
	*/
	function onContentPrepare( $context, &$article, &$params, $page = 0)
		{
		global $mainframe;
	
		if ( JString::strpos( $article->text, 'http://vimeo.com/' ) === false ) {
		return true;
		}
	
		$article->text = preg_replace('|(http://vimeo.com/([a-zA-Z0-9_-]+))|e', '$this->vimeoCodeEmbed("\2")', $article->text);

		return true;
	
	}

	function vimeoCodeEmbed( $vCode )
	{

		$output = '';
		$params = $this->params;

		$width = $params->get('width', 425);
		$height = $params->get('height', 344);
		$responsive = $params->get('responsive', 1);

		if( $responsive ){
		    $doc = JFactory::getDocument();
		    $doc->addStyleSheet(JURI::base() . "plugins/content/vimeoembed/style.css");
		    $output .= '<div class="vimeo-responsive">';
		}

		$output .= '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" src="http://player.vimeo.com/video/'.$vCode.'?portrait=0"></iframe>';

		if( $responsive ){
		    $output .= '</div>';
		}

		return $output;
	}

}
