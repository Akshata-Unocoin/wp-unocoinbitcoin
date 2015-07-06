<?php
/**
 * Plugin Name: WP UnocoinBitcoin
 * Plugin URI: http://www.unocoin.com
 * Description: Accept bitcoin payments via Unocoin from your WordPress site
 * Version: 1.1
 * Author: Tips and Tricks HQ
 * Author URI: http://www.unocoin.com
 * Requires at least: 3.0
*/

if (!defined('ABSPATH')) exit;

if (!class_exists('WP_UnocoinBitcoin')){

    class WP_UnocoinBitcoin{
        var $version = '1.1';
        var $db_version = '1.0';
        var $plugin_url;
        var $plugin_path;
        var $wp_unocoinbitcoin_settings_obj;

        function __construct() {
                $this->define_constants();
                $this->includes();
                $this->loader_operations();
                //Handle any db install and upgrade task
                add_action( 'init', array( &$this, 'plugin_init' ), 0 );
                add_action( 'admin_init', array( &$this, 'admin_init' ), 0);
                add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_scripts' ), 0 );
                add_action( 'wp_footer', array( &$this, 'plugin_footer' ), 0 );
                add_shortcode('wpbc_buy_now', array( &$this, 'wpbc_buy_now_handler' ), 0 );
        }

        function define_constants(){
                define('WP_UNOCOINBITCOIN_ADDON_VERSION', $this->version);
                define('WP_UNOCOINBITCOIN_SITE_URL',site_url());
                define('WP_UNOCOINBITCOIN_PLUGIN_URL', $this->plugin_url());
                define('WP_UNOCOINBITCOIN_PLUGIN_PATH', $this->plugin_path());
                define('WP_UNOCOINBITCOIN_PLUGIN_DB_VERSION', $this->db_version);		
        }

        function includes() {
            include_once('wpbc-config.php');
            include_once('gateway-settings.php');
            include_once('filters-and-hooks.php');
            include_once('wpbc-form.php');
            include_once('process-checkout.php');
        }

        function loader_operations(){
                register_activation_hook( __FILE__, array(&$this, 'activate_handler') );//activation hook
                add_action('plugins_loaded',array(&$this, 'plugins_loaded_handler'));//plugins loaded hook		
        }

        function plugins_loaded_handler(){//Runs when plugins_loaded action gets fired
                $this->do_db_upgrade_check();
                $this->wp_unocoinbitcoin_settings_obj = new WP_UnocoinBitcoin_Settings_Page();//Initialize settins menus
        }

        function activate_handler()  //Will run when the plugin activates only
        {
            add_option('wpbc_plugin_version', $this->version);
            // If the page doesn't already exist, then create it
            $config = WP_UnocoinBitcoin_Config::getInstance();
            $order_page_url = $config->getValue('wpbc_unocoin_form_page_url');
            if(empty($order_page_url))
            {
                $slug = 'wpbc-order-info';
                $page_title = 'Order Information';
                $page_content = 'This page has been created by WP Unocoin Bitcoin plugin. Please do not delete it. You can hide this page from your navigation menu.';
                $page_data = array(
                'post_status' 		=> 'publish',
                'post_type' 		=> 'page',
                'post_author' 		=> 1,
                'post_name' 		=> $slug,
                'post_title' 		=> $page_title,
                'post_content' 		=> $page_content,
                'comment_status' 	=> 'closed'
                );
                $page_id = wp_insert_post($page_data);  
                $permalink = get_permalink($page_id);
                $config->setValue('wpbc_unocoin_form_page_url', $permalink);
            }
            $config->saveConfig();
        }

        function do_db_upgrade_check()
        {
            //NOP
            if(is_admin())
            {
                $wpbc_version = get_option('wpbc_plugin_version');
                if(!isset($wpbc_version) || $wpbc_version != $this->version)
                {
                    $this->activate_handler();
                }
            }
        }

        function plugin_init(){
                //add_filter('ngg_render_template',array(&$this, 'ngg_render_template_handler'),10,2);
                if(is_admin())
                {
                    wpbc_bitcoin_create_order_page();
                }
                if(!is_admin())
                {
                    wpbc_unocoin_handle_post_payment();
                }
        }
        
        function admin_init()
        {
            wpbc_bitcoin_add_meta_boxes();
        }

        function plugin_scripts()
        {
            if (!is_admin()) 
            {
                //wp_enqueue_script( 'jquery' );
            }
        }

        function plugin_footer()
        {

        }

        function plugin_url() { 
                if ( $this->plugin_url ) return $this->plugin_url;
                return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
        }

        function plugin_path() { 	
                if ( $this->plugin_path ) return $this->plugin_path;		
                return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
        }
        
        function wpbc_buy_now_handler($atts)
        {
            extract(shortcode_atts(array(
                'item_name' => '',
                'currency' => 'INR',
                'price' => '',
                'image' => '',
                'gateway' => 'unocoin'
            ), $atts));
            if(empty($image)){
                $image = WP_UNOCOINBITCOIN_PLUGIN_URL.'/images/wpbc_co.png';
            }
            $config = WP_UnocoinBitcoin_Config::getInstance();
            $order_page = $config->getValue('wpbc_unocoin_form_page_url');
            $action_url = add_query_arg( array('wpbc_co' => '1', 'gateway' => $gateway), $order_page );
            $output = <<<EOT
            <form action="$action_url" method="post">
            <input type="hidden" name="item_name" value="$item_name">
            <input type="hidden" name="price" value="$price">
            <input type="hidden" name="currency" value="$currency">
            <input type="image" src="$image" name="submit">
            </form>
EOT;
            return $output;
        }
        
        public static function get_text_message()
        {
            $text = array(
                "PROCESSING_ORDER" => "Processing Order...",
                "ORDER_BEING_PROCESSED" => "Please wait, your order is being processed... You will be redirected to the payment page shortly",
                "NOT_AUTO_REDIRECT" => "If you are not automatically redirected to the payment page within 5 seconds...",
                "CLICK_HERE" => "Click Here",
                // Billing Section of Payment Form
                "BILLING_INFORMATION" => "Billing Information",
                "BILLING_FIRST_NAME" => "First Name:",
                "BILLING_LAST_NAME" => "Last Name:",
                "BILLING_ADDRESS" => "Address:",
                "BILLING_CITY" => "City:",
                "BILLING_STATE_PROVINCE" => "State/Province:",
                "BILLING_ZIP_POSTAL_CODE" => "ZIP/Postal Code:",
                "SELECT_BILLING_COUNTRY" => "Please Select",
                "BILLING_COUNTRY" => "Country:",
                "BILLING_EMAIL" => "Email:",
                "BILLING_PHONE" => "Phone:",
                // Shipping Section of Payment Form
                "SHIPPING_INFORMATION" => "Shipping Information",
                "SAME_AS_BILLING_INFORMATION" => "Same as Billing Details?",
                "SHIPPING_FIRST_NAME" => "First Name:",
                "SHIPPING_LAST_NAME" => "Last Name:",
                "SHIPPING_ADDRESS" => "Shipping Address:",
                "SHIPPING_CITY" => "City:",
                "SHIPPING_STATE_PROVINCE" => "State/Province:",
                "SHIPPING_ZIP_POSTAL_CODE" => "ZIP/Postal Code:",
                "SELECT_SHIPPING_COUNTRY" => "Please Select",
                "SHIPPING_COUNTRY" => "Country:",
                "SHIPPING_EMAIL" => "Email:",
                "SHIPPING_PHONE" => "Phone:",
                // Credit Card Section of Payment Form
                "CREDIT_CARD_INFORMATION" => "Credit Card Information",
                "CREDIT_CARD_I_HAVE" => "I have:",
                "CREDIT_CARD_NUMBER" => "Card Number:",
                "NAME_ON_CREDIT_CARD" => "Name on Card:",
                "CREDIT_CARD_EXPIRATION_DATE" => "Expiration Date:",
                "CREDIT_CARD_CVV" => "CVV:",
                "CREDIT_CARD_CVV_HELP_TEXT" => "For MasterCard or Visa, it is the last three digits in the signature area on the back of your card. For American Express, it is the four digits on the front of the card.",
            );
            return $text;
        }
    }//End of class

}//End of class not exists check

$GLOBALS['wp_unocoinbitcoin'] = new WP_UnocoinBitcoin();
