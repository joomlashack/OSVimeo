<?php
/**
 * @package   plg_content_osvimeo
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;

defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');

include_once 'include.php';

if (defined('OSVIMEO_LOADED')) {
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
            if (JString::strpos($article->text, '://vimeo.com/') === false) {
                return true;
            }

            $this->init();

            if ($this->isPro()) {
                $regex = Alledia\OSVimeo\Pro\Embed::getRegex();
            } else {
                $regex = '#https?://(?:www\.)?vimeo.com/([0-9]+)#i';
            }

            if (preg_match_all($regex, $article->text, $matches)) {
                foreach ($matches[0] as $k => $url) {
                    $videoID = $matches[1][$k];

                    // Ignores some know invalid urls
                    $invalidIDs = array('channels', 'moogaloop');
                    if (!in_array($videoID, $invalidIDs)) {
                        $article->text = str_replace(
                            $url,
                            $this->vimeoCodeEmbed($videoID),
                            $article->text
                        );
                    }
                }
            }

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
                'width'                 => $width,
                'height'                => $height,
                'src'                   => '//player.vimeo.com/video/' . $vCode,
                'frameborder'           => '0',
                'webkitallowfullscreen' => 'webkitallowfullscreen',
                'mozallowfullscreen'    => 'mozallowfullscreen',
                'allowfullscreen'       => 'allowfullscreen',
            );

            if ($this->isPro()) {
                $attribs = Alledia\OSVimeo\Pro\Embed::setAttributes($params, $attribs);
            }

            $output .= '<iframe name="vimeo_' . $vCode . '" ' . JArrayHelper::toString($attribs) . '></iframe>';

            if ($responsive) {
                $output .= '</div>';
            }

            return $output;
        }
    }
}
