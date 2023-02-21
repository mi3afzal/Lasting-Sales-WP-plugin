<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://lastingsales.com
 * @since      1.0.0
 *
 * @package    Lasting_Sales
 * @subpackage Lasting_Sales/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Lasting_Sales
 * @subpackage Lasting_Sales/admin
 * @author     LastingSales <support@lastingsales.com>
 */
class Lasting_Sales_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $lasting_sales    The ID of this plugin.
	 */
	private $lasting_sales;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $lasting_sales       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 *@since    1.0.0
	 */
	public function __construct($lasting_sales, $version ) {

		$this->lasting_sales = $lasting_sales;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lasting_Sales_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lasting_Sales_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style( 'lasting_sales_admin_css', plugin_dir_url( __FILE__ ) . 'css/lasting-sales-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/libs/bootstrap.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Lasting_Sales_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Lasting_Sales_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->lasting_sales, plugin_dir_url( __FILE__ ) . 'js/lasting-sales-admin.js', array( 'jquery' ), $this->version, false );
        require_once plugin_dir_path( __FILE__ ) . 'partials/lasting-sales-admin-display.php';
	}

    public function lasting_sales_plugin_menu()
    {
        add_menu_page("LastingSales", "LastingSales", 'manage_options', "lasting-sales-admin-display", "render_admin_page", plugins_url( 'lasting-sales/admin/images/favicon.png' ));
    }
}
