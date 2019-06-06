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

use Alledia\Framework;

defined('_JEXEC') or die();

define('OSVIMEO_PLUGIN_PATH', __DIR__);

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (file_exists($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;
    } else {
        if ($app = Framework\Factory::getApplication()) {
            if ($app->isClient('administrator')) {
                $app->enqueueMessage('[OSVimeo] Alledia framework not found', 'error');
            }
        }
    }
}

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    if (file_exists(OSVIMEO_PLUGIN_PATH . '/library')
        && !class_exists('Alledia\OSVimeo\Pro\Embed')
    ) {
        Framework\AutoLoader::register('Alledia\\OSVimeo', OSVIMEO_PLUGIN_PATH . '/library');
    }

    if (!defined('OSVIMEO_LOADED')) {
        define('OSVIMEO_LOADED', 1);
    }
}
