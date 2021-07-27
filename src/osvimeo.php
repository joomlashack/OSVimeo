<?php
/**
 * @package   OSVimeo
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2021 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSVimeo.  If not, see <https://www.gnu.org/licenses/>.
 */

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

if (!include_once 'include.php') {
    return;
}

class PlgContentOsvimeo extends AbstractPlugin
{
    public $namespace = 'OSVimeo';

    /**
     * @param string   $context
     * @param object   $article
     * @param Registry $params
     * @param int      $page
     *
     * @return bool
     */
    public function onContentPrepare($context, $article, $params, $page = 0)
    {
        if (StringHelper::strpos($article->text, '://vimeo.com/') === false) {
            return true;
        }

        $this->init();

        $replacements = [];
        $ignoreLinks  = $this->params->get('ignore_html_links', 0);
        $regex        = '(http|https)?:\/\/(www\.|player\.)?vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|video\/|)(\d+)(?:|\/\?)';
        $linkRegex    = '#(?:<a.*href=[\'"])' . addcslashes($regex, '#') . '(?:[\'"].*>.*</a>)#i';

        if (preg_match_all($linkRegex, $article->text, $matches)) {
            foreach ($matches[0] as $k => $source) {
                $videoID    = $matches[4][$k];
                $replaceKey = sprintf('{{%s}}', md5($source));

                if (!isset($replacements[$replaceKey])) {
                    $replacements[$replaceKey] = $ignoreLinks ? $source : $this->vimeoCodeEmbed($videoID);
                }

                $article->text = str_replace(
                    $source,
                    $replaceKey,
                    $article->text
                );
            }
        }

        $regex = '#https?://(?:www\.)?vimeo.com/((?:[0-9]+)(?:[0-9?&a-z=_\-]*)?)#i';
        if (preg_match_all($regex, $article->text, $matches)) {
            foreach ($matches[0] as $k => $url) {
                $videoID    = $matches[1][$k];
                $replaceKey = sprintf('{{%s}}', md5($url));

                // Ignores some know invalid urls
                $invalidIDs = ['channels', 'moogaloop'];
                if (!in_array($videoID, $invalidIDs) && !isset($replacements[$replaceKey])) {
                    $article->text = str_replace(
                        $url,
                        $this->vimeoCodeEmbed($videoID),
                        $article->text
                    );
                }
            }
        }

        if ($replacements) {
            $article->text = str_replace(array_keys($replacements), $replacements, $article->text);
        }

        return true;
    }

    /**
     * @param string $videoId
     *
     * @return string
     */
    protected function vimeoCodeEmbed(string $videoId): string
    {
        $output = '';
        $params = $this->params;

        $width      = $params->get('width', 425);
        $height     = $params->get('height', 344);
        $responsive = $params->get('responsive', 1);

        if ($responsive) {
            HTMLHelper::_('stylesheet', 'plg_content_osvimeo/style.css', ['relative' => true]);
            $output .= '<div class="vimeo-responsive">';
        }

        $query   = explode('&', htmlspecialchars_decode($videoId));
        $videoId = array_shift($query);
        if ($query) {
            $videoId .= '?' . http_build_query($query);
        }

        $attribs = [
            'width'                 => $width,
            'height'                => $height,
            'src'                   => '//player.vimeo.com/video/' . $videoId,
            'frameborder'           => '0',
            'webkitallowfullscreen' => 'webkitallowfullscreen',
            'mozallowfullscreen'    => 'mozallowfullscreen',
            'allowfullscreen'       => 'allowfullscreen',
        ];

        if ($this->isPro()) {
            $attribs = Alledia\OSVimeo\Pro\Embed::setAttributes($params, $attribs);
        }

        $output .= sprintf('<iframe name="vimeo_%s" %s></iframe>', $videoId, ArrayHelper::toString($attribs));

        if ($responsive) {
            $output .= '</div>';
        }

        return $output;
    }
}
