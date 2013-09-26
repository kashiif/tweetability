<?php
/**
 * Admin class for Tweetability
 *
 * @package Tweetability
 * @author  Kashif Iqbal Khan <kashiif@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2013 Kashif Iqbal Khan kashiif@gmail.com 
 * @link      

 */

class Tweetability_Admin {

  /**
   * Instance of this class.
   *
   * @since    0.1.0
   * @var      object
   */
  protected static $instance = null;
  
  
  /**
   * Slug of the plugin options screen.
   *
   * @since    0.1.0
   * @var      string
   */
  protected $plugin_screen_hook_suffix = null;

  protected $settings = null;

  /**
   * Initialize the plugin settings page.
   *
   * @since     1.0.0
   */
  private function Tweetability_Admin() {

  }

  /**
   * Return an instance of this class.
   *
   * @since     1.0.0
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
  * Adds the settings link on plugins page
  */
  public static function filter_action_links( $links, $file ) {
    if ( $file != Tweetability_Info::$plugin_basename )
      return $links;

    $settings_link = '<a href="' . menu_page_url( Tweetability_Info::settings_page_slug, false ) . '">'
      . esc_html( __( 'Settings', 'tweetability' ) ) . '</a>';

    array_push( $links, $settings_link );

    return $links;
  }

  /**
   * Handles admin_menu action
   *
   * @since    0.1.0
   */
  public function do_admin_menu() {
    self::get_instance()->_do_admin_menu();
  }
    
  /**
   * Register the administration menu for this plugin into the WordPress Dashboard menu.
   *
   * @since    0.1.0
   */
  private function _do_admin_menu() {
    $this->plugin_screen_hook_suffix = add_options_page( 
            __('SETTING_PAGE_TITLE', 'tweetability'),     /* The title of the page when the menu is selected */
            __('SETTING_PAGE_MENU_LABEL', 'tweetability'),/* The text for the menu */
            'manage_options',                              /* capability required for this menu to be displayed to user */
            Tweetability_Info::settings_page_slug , /* menu slug that is used when adding setting sections */
            array($this, 'add_options_page')               /* callback to output the content for this page */
          );
    
    // Load admin style sheet and JavaScript.
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    
  }
  
  /**
   * Register and enqueue admin-specific style sheet.
   *
   * @since     0.1.0
   * @return    void
   */
  public function enqueue_admin_styles() {
    // Return early if no settings page is registered
    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_style( Tweetability_Info::slug .'-admin-styles',
                        Tweetability_Info::$plugin_url . '/css/admin.css', 
                        array(),
                        Tweetability_Info::version );
    }
  }

  /**
   * Register and enqueue admin-specific JavaScript.
   *
   * @since     0.1.0
   * @return    void
   */
  public function enqueue_admin_scripts() {
    // Return early if no settings page is registered
    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_script( Tweetability_Info::slug . '-admin-script', 
                         Tweetability_Info::$plugin_url . '/js/admin.js',
                         array( 'jquery' ),
                        Tweetability_Info::version );
    }
  }
  
  public function add_options_page() {
    ?>
    <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php printf( __('SETTING_PAGE_TITLE', 'tweetability') ) ?></h2>
      <form action="options.php" method="POST">
        <?php settings_fields( 'tweetability-settings-group' ); ?>
        <?php do_settings_sections( Tweetability_Info::settings_page_slug ); ?>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  /**
  * Handles admin_init action
  *
  * @since    0.1.0
  */
  public static function do_admin_init() {
    self::get_instance()->_do_admin_init();
  }
    
  /**
  * Adds sections and fields on settings page using settings API
  *
  * @since    0.1.0
  */
  private function _do_admin_init() {
    $this->settings = get_option( 'tweetability-settings' );

    register_setting( 'tweetability-settings-group', 'tweetability-settings', array($this, 'validate_plugin_options') );
   
    $page = Tweetability_Info::settings_page_slug;

    // Make 1 Section
    $sections = array(
                  'general_settings_section'
                );
    
    $this->add_section( $sections[0], __('GENERAL_SETTING_TITLE', 'tweetability'), 'add_general_settings_section' );

    // Fields - General Settings section
    add_settings_field( 'tweetability-via', __('VIA', 'tweetability'), array($this, 'add_setting'), $page, $sections[0], array( 'via', __('HELP_TEXT_VIA', 'tweetability') ) );
    add_settings_field( 'tweetability-related', __('RELATED', 'tweetability'), array($this, 'add_setting'), $page, $sections[0], array( 'related', __('HELP_TEXT_RELATED', 'tweetability') ) );
    add_settings_field( 'tweetability-linkclass', __('LINK_CLASS', 'tweetability'), array($this, 'add_setting'), $page, $sections[0], array( 'linkclass', __('HELP_TEXT_LINKCLASS', 'tweetability') ) );
    add_settings_field( 'tweetability-tooltip', __('TOOLTIP', 'tweetability'), array($this, 'add_setting'), $page, $sections[0], array( 'tooltip', __('HELP_TEXT_TOOLTIP', 'tweetability') ) );
  }
  
  private function add_section($section, $localized_title, $function_name) {
    add_settings_section( $section, /* string for use in the 'id' attribute of tags */
                          $localized_title, /* title of the section */
                          array($this, $function_name), /* function that fills the section with the desired content */
                          Tweetability_Info::settings_page_slug /* The menu slug on which to display this section. should be same as passed in add_options_page() */
                        );
  }

  private function add_textbox($field_name, $help_text) {
    $field = esc_attr( $this->settings[$field_name] );
    echo "<input class='regular-text' type='text' name='tweetability-settings[$field_name]' value='$field' />";
    if ($help_text) {
      echo "<p class='description'>" . $help_text . "</p>";
    }
  }

  
  /********************** General Settings Related **********************/

  /**
  * Displays the descriptive text for general settings section
  * Could be any html
  */
  public function add_general_settings_section() {
    printf(__('GENERAL_SETTING_AREA_DESCRIPTION', 'tweetability'));
  }

  public function add_setting($args) {
      $this->add_textbox( $args[0], $args[1] );
  }

  /********************** Validation for Options Form **********************/
  
  /**
   * Sanitize user input before it gets saved to database
   */
  public function validate_plugin_options($inputs) {

    // retrieve old settings
    $options = get_option('tweetability-settings');

    /*
    $val = trim($input['req-setting-1']);
    if(preg_match('/^[a-z0-9]{32}$/i', $val)) { // put validation logic for req-setting-1 here
      $options['req-setting-1'] = $val;    
    }
    else {
      $options['req-setting-1'] = '';
    }
    */
    
    // return the settings that you want to be saved.
    return $inputs;
  }
  

}

add_action( 'admin_init', array('Tweetability_Admin','do_admin_init') );
add_action( 'admin_menu', array('Tweetability_Admin','do_admin_menu') );

add_filter( 'plugin_action_links', array('Tweetability_Admin','filter_action_links'), 10, 2 );