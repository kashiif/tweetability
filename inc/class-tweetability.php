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

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Load public-facing style sheet and JavaScript.
		
    // TODO add following actions only for WP < 3.3
    //add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );    
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    
    add_action( 'wp_footer', array( $this, 'print_inline_script' ) ); 
    
		add_shortcode( 'tweetability', array( $this, 'handle_shortcode' ) );
    
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
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		$domain = Tweetability_Info::slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, Tweetability_Info::slug . '/lang/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( Tweetability_Info::slug . '-plugin-styles', Tweetability_Info::$plugin_url . '/css/public.css', array(), Tweetability_Info::version );
	}
  
	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( Tweetability_Info::slug . '-plugin-script', Tweetability_Info::$plugin_url . '/js/jquery.tweetable.js', array( 'jquery' ), Tweetability_Info::version );
	}
  
  
  public function handle_shortcode($attrs, $content) {
    // enqueuing scripts here works only in WP 3.3 and above
    // See: http://scribu.net/wordpress/conditional-script-loading-revisited.html
    // TODO: compatability WP< 3.3
    $this->enqueue_styles();
    $this->enqueue_scripts();
    
    // error_log("handle_shortcode: " . $content . " - " . json_encode($attrs));
    if (strlen($content) == 0) {
      return "";
    }

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

  function print_inline_script() {
    if ( wp_script_is( Tweetability_Info::slug . '-plugin-script', 'enqueued' ) ) {
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
  
}
