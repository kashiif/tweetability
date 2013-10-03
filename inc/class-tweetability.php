<?php
/**
 * Plugin class.
 *
 * @package Tweetability
 * @author  Kashif Iqbal Khan <kashiif@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2013 Kashif Iqbal Khan kashiif@gmail.com 
 * @link      

 */
class Tweetability {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 * @var      object
	 */
	protected static $instance = null;

  private $shortcode_found = false;

  /**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1.0
	 */
	private function Tweetability() {

		// Handle init
		add_action( 'init', array( $this, 'handle_init' ) );

    // Lets enqueue script and style conditionally in the_posts handler
    add_filter( 'the_posts', array( $this, 'handle_the_posts') );

    // Print the inline script in footer
    add_action( 'wp_footer', array( $this, 'handle_footer') );

		add_shortcode( 'tweetability', array( $this, 'handle_shortcode' ) );
    
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
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
   * Handles init action.
   *
   * @since     0.2.0
   * @return    object    A single instance of this class.
   */
  public function handle_init() {
    $this->load_plugin_textdomain();
    $this->register_script_and_style();
  }

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	private function load_plugin_textdomain() {

		$domain = Tweetability_Info::slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, Tweetability_Info::slug . '/lang/' );
	}

  /**
   * Register public-facing script and style sheet. These would be enqueued later only when necessary.
   *
   * @since    0.2.0
   */
  private function register_script_and_style() {
    wp_register_script( Tweetability_Info::slug . '-plugin-script',
                        Tweetability_Info::$plugin_url . '/js/jquery.tweetable.js',
                        array( 'jquery' ),
                        Tweetability_Info::version );

    wp_register_style( Tweetability_Info::slug . '-plugin-style',
                      Tweetability_Info::$plugin_url . '/css/public.css',
                      array(),
                      Tweetability_Info::version );
  }

  /**
   * Handles the_posts filter. Checks if the psots have the shortcode and enqueue script and stylesheet accordingly.
   *
   * @since    0.2.0
   */
  public function handle_the_posts($posts) {
    if (empty($posts)) return $posts;

    $this->shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
    foreach ($posts as $post) {
      if (stripos($post->post_content, '[tweetability]') !== false) {
        $this->shortcode_found = true; // bingo!
        break;
      }
  	}

    if ($this->shortcode_found) {
      // enqueue here
      wp_enqueue_style(Tweetability_Info::slug . '-plugin-style');
      wp_enqueue_script(Tweetability_Info::slug . '-plugin-script');
    }
    return $posts;
  }

  public function handle_footer() {
    if ( ! $this->shortcode_found )
      return;

    self::print_inline_script();
  }


  public function handle_shortcode($attrs, $content) {
    // enqueuing scripts here works only in WP 3.3 and above
    // See: http://scribu.net/wordpress/conditional-script-loading-revisited.html
    // TODO: compatability WP< 3.3
    //$this->enqueue_styles();
    //$this->enqueue_scripts();

    // error_log("handle_shortcode: " . $content . " - " . json_encode($attrs));
    if (strlen($content) == 0) {
      return "";
    }

    //this->$add_script = true;

    $settings = get_option( 'tweetability-settings' );
    $defaults = array(
        'via' => $settings['via'],
        'related' => $settings['related'],
        'linkclass' => $settings['linkclass'],
        'url' => '',
      );

    // override global settings with the ones specified in shortcode
    $attrs = shortcode_atts($defaults, $attrs);

    $attrs_string = self::get_attr_as_string($attrs, 'via')
                  . self::get_attr_as_string($attrs, 'related')
                  . self::get_attr_as_string($attrs, 'url')
                  . self::get_attr_as_string($attrs, 'linkclass');
    
    return "<span class='tweetability-plugin' $attrs_string><span>" . $content . "</span></span>";
  }
  
  private static function get_attr_as_string($attrs, $name) {
    $val = $attrs[$name];
    return isset($val) && strlen($val)? " data-$name='$val'": "";
  }

  private static function print_inline_script() {
      ?>
    <script type="text/javascript">
    jQuery(function($){

    var config = ["via", "linkClass", "related"];
      $(".tweetability-plugin").each(function(index, item){

        var $this = $(item),
            opts = {};

        for (var i=0; i<config.length;i++) {
          var propName = config[i],
              attrVal = $this.attr("data-" + propName);

          if (attrVal) {
            opts[propName] = attrVal;
          }
        }
        $this.find("span").tweetable(opts);
      });
    });
    </script>
    <?php
  }
  
}
