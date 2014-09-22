<?php
/**
 * @package   plg_content_osvimeo
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

require_once 'include.php';

/**
 * OSVimeo Content Plugin
 *
 */
class plgContentOSVimeo extends JPlugin
{
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);

        $lang = JFactory::getLanguage();
        $lang->load('plg_content_osvimeo.sys', __DIR__);
    }

    /**
     * @param string $context
     * @param object $article
     * @param object $params
     * @param int    $page
     *
     * @return bool
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if (JString::strpos($article->text, 'http://vimeo.com/') === false) {
            return true;
        }

        $article->text = preg_replace(
            '|(http://vimeo.com/([a-zA-Z0-9_-]+))|e',
            '$this->vimeoCodeEmbed("\2")',
            $article->text
        );

        return true;
    }

    protected function vimeoCodeEmbed($vCode)
    {
        $output = '';
        $params = $this->params;

        $width      = $params->get('width', 425);
        $height     = $params->get('height', 344);
        $responsive = $params->get('responsive', 1);

        if ($responsive) {
            $doc = JFactory::getDocument();
            $doc->addStyleSheet(JURI::base() . "plugins/content/osvimeo/style.css");
            $output .= '<div class="vimeo-responsive">';
        }

        $query = explode('&', htmlspecialchars_decode($vCode));
        $vCode = array_shift($query);
        if ($query) {
            $vCode .= '?' . http_build_query($query);
        }

        $attribs = array(
            'width'       => $width,
            'height'      => $height,
            'src'         => '//player.vimeo.com/video' . $vCode,
            'frameborder' => '0'
        );

        if (OSVIMEO_PRO) {
            $attribs = OSVimeoPro\Embed::setAttributes($params, $attribs);
        }

        $output .= '<iframe ' . JArrayHelper::toString($attribs) . '></iframe>';

        if ($responsive) {
            $output .= '</div>';
        }

        return $output;
    }
}
