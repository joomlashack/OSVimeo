<?php
/**
 * @package   OSVimeo
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Alledia\Factory;
use Alledia\Framework;

defined('_JEXEC') or die();

define('OSVIMEO_PLUGIN_PATH', __DIR__);

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (file_exists($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;
    } else {
        if ($app = JFactory::getApplication()) {
            if ($app->isAdmin()) {
                $app->enqueueMessage('[OSVimeo] Alledia framework not found', 'error');
            }
        }
    }
}

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    if (file_exists(OSVIMEO_PLUGIN_PATH . '/library')
        && !class_exists('Alledia\OSVimeo\Pro\Embed')) {

        Framework\AutoLoader::register('Alledia\\OSVimeo', OSVIMEO_PLUGIN_PATH . '/library');
    }

    if (!defined('OSVIMEO_LOADED')) {
        define('OSVIMEO_LOADED', 1);
    }
}
