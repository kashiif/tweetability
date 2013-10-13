<?php
/**
 * Tweetability allows you to indicate text in your posts that your readers can tweet with a single click by using a
 * shortcode.
 *
 * @package Tweetability
 * @author  Kashif Iqbal Khan <kashiif@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2013 Kashif Iqbal Khan kashiif@gmail.com
 * @link      www.kashiif.com
 *
 * @wordpress-plugin
 * Plugin Name: Tweetability
 * Plugin URI:  https://wordpress.org/plugins/tweetability/
 * Description: Make sentences in your post tweetable - The modern way
 * Version:     0.1.0
 * Author:      Kashif Iqbal Khan
 * Author URI:  www.kashiif.com
 * Text Domain: tweetability-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Tweetability MetaData class.
 *
 * Defines useful constants
 */

class Tweetability_Info {
  /**
   * Unique identifier for your plugin.
   *
   * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
   * match the Text Domain file header in the main plugin file.
   *
   * @since    0.1.0
   * @var      string
   */
  const slug = 'tweetability';
  //const base_name = 'tweetability';
  const settings_page_slug = 'tweetability-options';


  /**
   * Version of the plugin.
   *
   * @since    0.1.0
   * @var      string
   */
  const version = '0.2.0';

  const required_wp_version = '3.0';

  public static $plugin_dir = '';

  public static $plugin_url = '';

  public static $plugin_basename = '';

  /**
   * This function is called once on plugin load. It will initialize the static variables and also sets up appropriate hooks .
   *
   * @since    0.2.0
   */
  public static function init() {
    self::$plugin_dir = untrailingslashit(dirname(__FILE__));
    self::$plugin_url = untrailingslashit(plugins_url('', __FILE__));
    self::$plugin_basename = plugin_basename(__FILE__);

    add_action( 'admin_menu', array('Tweetability_Info','handle_admin_menu') );
  }

  public static function handle_admin_menu() {
    require(plugin_dir_path(__FILE__) . 'inc/admin-settings.php');
    $admin = Tweetability_Admin::get_instance();
    $admin->handle_admin_menu();
  }

}

Tweetability_Info::init();

// include plugin's class file
require(plugin_dir_path(__FILE__) . 'inc/class-tweetability.php');

Tweetability::get_instance();