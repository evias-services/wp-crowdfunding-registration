<?php
/*
* Plugin Name: Crowdfunding Registration Form
* Plugin URI: https://wordpress.org/plugins/wp-crowdfunding-registration
* Description: WP Crowdfunding Registration Form
* Version: 1.0.0
* Author: Gregory Saive
* Author URI: https://evias.be
* License: GNU/GPL v3
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if(!defined('ABSPATH')) exit;

define('CFREG_FILE', __FILE__);
define('CFREG_VERSION', '0.0.2');
define('CFREG_DIR_URL', plugin_dir_url( CFREG_FILE ));
define('CFREG_DIR_PATH', plugin_dir_path( CFREG_FILE ));
define('CFREG_BASENAME', plugin_basename( CFREG_FILE ));

if (!class_exists( 'CROWDFUNDING_REGISTRATION' )) {
    require_once CFREG_DIR_PATH . 'includes/class-evs-crowdfunding-registration.php';
    new \evias\CROWDFUNDING_REGISTRATION();
}
