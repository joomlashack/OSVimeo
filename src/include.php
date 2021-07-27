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

use Alledia\Framework\AutoLoader;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (is_file($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;

    } elseif (
        ($app = Factory::getApplication())
        && $app->isClient('administrator')
    ) {
        $app->enqueueMessage('[OSVimeo] Alledia framework not found', 'error');

        return false;
    }
}

if (!defined('OSVIMEO_LOADED')) {
    AutoLoader::register('Alledia\\OSVimeo', __DIR__ . '/library');

    define('OSVIMEO_LOADED', true);
}

return defined('OSVIMEO_LOADED');
