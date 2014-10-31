<?php
/**
 * @package   plg_content_osvimeo
 * @contact   www.alledia.com, hello@alledia.com
 * @copyright 2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

require_once 'include.php';

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    /**
     * OSVimeo Content Plugin
     *
     */
    class PlgContentOSVimeo extends AbstractPlugin
    {
        public function __construct(&$subject, $config = array())
        {
            $this->namespace = 'OSVimeo';

            parent::__construct($subject, $config);
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

            $this->init();

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
                'src'         => '//player.vimeo.com/video/' . $vCode,
                'frameborder' => '0'
            );

            if ($this->isPro()) {
                $attribs = Alledia\OSVimeo\Pro\Embed::setAttributes($params, $attribs);
            }

            $output .= '<iframe ' . JArrayHelper::toString($attribs) . '></iframe>';

            if ($responsive) {
                $output .= '</div>';
            }

            return $output;
        }
    }
}
