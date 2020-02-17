<?php
/**
 * @package   OSVimeo
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSVimeo.
 *
 * OSVimeo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSVimeo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSVimeo.  If not, see <http://www.gnu.org/licenses/>.
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

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
            if (StringHelper::strpos($article->text, '://vimeo.com/') === false) {
                return true;
            }

            $this->init();

            $regex = '#https?://(?:www\.)?vimeo.com/((?:[0-9]+)(?:[0-9?&a-z=_\-]*)?)#i';

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
                JHtml::_('stylesheet', 'plugins/content/osvimeo/style.css');
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

            $output .= sprintf('<iframe name="vimeo_%s" %s></iframe>', $vCode, ArrayHelper::toString($attribs));

            if ($responsive) {
                $output .= '</div>';
            }

            return $output;
        }
    }
}
