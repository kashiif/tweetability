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
   * Handles filter_action_links filter. Adds the settings link on plugins page
   *
   * @since    0.1.0
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
   * Register the administration menu for this plugin into the WordPress Dashboard menu.
   *
   * @since    0.1.0
   */
  public function handle_admin_menu() {
    add_action( 'admin_init', array( $this, 'handle_admin_init') );
    $this->plugin_screen_hook_suffix = add_options_page(
            __('Tweetability Options', 'tweetability'),     /* The title of the page when the menu is selected */
            __('Tweetability', 'tweetability'),/* The text for the menu */
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
  public function enqueue_admin_styles($screen_suffix) {
    // Return early if no settings page is registered
    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    if ( $screen_suffix == $this->plugin_screen_hook_suffix ) {
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
   *
   * @return    void
   */
  public function enqueue_admin_scripts($screen_suffix) {
    // Return early if no settings page is registered
    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    if ( $screen_suffix == $this->plugin_screen_hook_suffix ) {
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
      <h2><?php printf( __('Tweetability Options', 'tweetability') ) ?></h2>
      <form action="options.php" method="POST">
      <?php 
        settings_fields( 'tweetability-settings-group' );
        do_settings_sections( Tweetability_Info::settings_page_slug );
        $this->render_submit_button();
      ?>

      </form>
    </div>
    <?php
  }

  private function render_submit_button() {
    // submit_button was introduced in WP 3.1.0 so fallback to submit button html for older versions
    if (function_exists('submit_button')) {
      submit_button();
      return;
    }
    ?>

    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo _( 'Save Changes' ); ?>"/>
    </p>

    <?php
  }

  /**
   * Handles admin_init action
   * Adds sections and fields on settings page using settings API
   *
   * @since    0.1.0
   */

  public function handle_admin_init() {
    add_filter( 'plugin_action_links', array('Tweetability_Admin','filter_action_links'), 10, 2 );

    $this->settings = get_option( 'tweetability-settings' );

    register_setting( 'tweetability-settings-group', 'tweetability-settings', array($this, 'validate_plugin_options') );
   
    $page = Tweetability_Info::settings_page_slug;

    // Make 1 Section
    $sections = array(
                  'general_settings_section'
                );
    
    $this->add_section( $sections[0], __('Global Settings', 'tweetability'), 'add_general_settings_section');

    // Fields - General Settings section
    add_settings_field( 'tweetability-via', __('Via', 'tweetability') . ' (via)', array($this, 'add_setting'), $page, $sections[0],
      array( 'via', __('A screen name to associate with the Tweet. The provided screen name will be appended to the end of the tweet with the text: "via @username"', 'tweetability') ) );
    add_settings_field( 'tweetability-related', __('Related', 'tweetability') . ' (related)', array($this, 'add_setting'), $page, $sections[0],
      array( 'related', __('Suggest accounts related to the your content or intention by comma-separating a list of screen names. After Tweeting, the user will be encouraged to follow these accounts.', 'tweetability') ) );
    add_settings_field( 'tweetability-linkclass', __('Link Class', 'tweetability') . ' (linkclass)', array($this, 'add_setting'), $page, $sections[0],
      array( 'linkclass', __('Additional css classes to add to twitter link. Thic can be used to change color, background color or hover color', 'tweetability') ) );
    add_settings_field( 'tweetability-tooltip', __('Tooltip', 'tweetability') . ' (tooltip)', array($this, 'add_setting'), $page, $sections[0],
      array( 'tooltip', __('Tooltip text that appears when user hovers over the link', 'tweetability') ) );
  }
  
  private function add_section($section, $localized_title, $function_name) {
    add_settings_section( $section, /* string for use in the 'id' attribute of tags */
                          $localized_title, /* title of the section */
                          array($this, $function_name), /* function that fills the section with the desired content */
                          Tweetability_Info::settings_page_slug /* The menu slug on which to display this section. should be same as passed in add_options_page() */
                        );
  }

  private function add_textbox($field_name, $help_text) {

    $field = isset($this->settings[$field_name]) ? esc_attr(  $this->settings[$field_name] ) : "";
    echo "<input class='regular-text' type='text' name='tweetability-settings[$field_name]' value='$field' />";
    if ($help_text) {
      echo "<p class='description'>" . esc_html($help_text) . "</p>";
    }
  }

  
  /********************** General Settings Related **********************/

  /**
  * Displays the descriptive text for general settings section
  * Could be any html
  */
  public function add_general_settings_section() {
    printf(__('Define values to be used globally for the shortcode.', 'tweetability'));
    printf(__('You can override any of these values by using associated attribute in the shortcode mentioned in parenthesis.', 'tweetability'));
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