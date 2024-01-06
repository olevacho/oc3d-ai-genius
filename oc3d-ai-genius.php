<?php

/*
  Plugin Name: OC3D AI Genius
  Plugin URI:
  Description: AI power for Wordpress. You can edit, generate texts, write programming codes using GPT. Plugin allows to create a database of instructions, which you can easily refer back to whenever needed. 
  Author: Oleh C...
  Author URI: https://github.com/olevacho/
  Text Domain: oc3d-ai-genius
  Domain Path: /lang
  Version: 1.0.1
  License:  GPL-2.0+
  License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! defined( 'ABSPATH' ) ) exit;
if (version_compare('5.3', phpversion(), '>')) {
    die(sprintf(__('We are sorry, but you need to have at least PHP 5.3 to run this plugin (currently installed version: %s) - please upgrade or contact your system administrator.'), phpversion()));
}

//Define constants

define('OC3DAIG_PREFIX', 'OC3DAIG_');
define('OC3DAIG_PREFIX_LOW', 'oc3daig_');
define('OC3DAIG_PREFIX_SHORT', 'oc3d_');
define('OC3DAIG_CLASS_PREFIX', 'Oc3dAig_');
define('OC3DAIG_PATH', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)));
define('OC3DAIG_URL', plugins_url('', __FILE__));
define('OC3DAIG_PLUGIN_FILE', __FILE__);
define('OC3DAIG_TEXT_DOMAIN', 'oc3d-ai-genius');
//Init the plugin
require_once OC3DAIG_PATH . '/lib/helpers/Utils.php';
require_once OC3DAIG_PATH . '/lib/Oc3dAig.php';
require_once OC3DAIG_PATH . '/lib/controllers/BaseController.php';
require_once OC3DAIG_PATH . '/lib/controllers/AdminController.php';

register_activation_hook(__FILE__, array('Oc3dAig', 'install'));
register_deactivation_hook(__FILE__, array('Oc3dAig', 'deactivate'));
register_uninstall_hook(__FILE__, array('Oc3dAig', 'uninstall'));
new Oc3dAig();
