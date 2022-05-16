<?php
/**
 * Plugin Name: Mizer Elements
 * Description: Custom Elementor widgets for Mizer Theme
 * Plugin URI: #
 * Author: wpconfigurator
 * Version: 1
 * Author URI: https://innwit.com/
 *
 * Text Domain: mizer-elements
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if (! defined('ME_VERSION')) {define( 'ME_VERSION', '1.0.0' );}
if (! defined('ME__FILE__')) {define( 'ME__FILE__', __FILE__ );}
if (! defined('ME_PLUGIN_BASE')) {define( 'ME_PLUGIN_BASE', plugin_basename( ME__FILE__ ) );}
if (! defined('ME_PATH')) {define( 'ME_PATH', plugin_dir_path( ME__FILE__ ) );}
if (! defined('ME_INCLUDES_PATH')) {define( 'ME_INCLUDES_PATH', ME_PATH . 'includes/' );}
if (! defined('ME_MODULES_PATH')) {define( 'ME_MODULES_PATH', ME_PATH . 'modules/' );}
if (! defined('ME_URL')) {define( 'ME_URL', plugins_url( '/', ME__FILE__ ) );}
if (! defined('ME_ASSETS_URL')) {define( 'ME_ASSETS_URL', ME_URL . 'assets/' );}
if (! defined('ME_MODULES_URL')) {define( 'ME_MODULES_URL', ME_URL . 'modules/' );}
if (! defined('Configurator_Template_Kits_SL_STORE_URL')) {define( 'Configurator_Template_Kits_SL_STORE_URL', 'https://wordpress-320165-1464446.cloudwaysapps.com/' );}
if (! defined('Configurator_Template_Kits_SL_ITEM_NAME')) {define( 'Configurator_Template_Kits_SL_ITEM_NAME', 'configurator-template-kits-blocks' );}



require_once ( ME_PATH . 'classes/class-menu-walker.php' );
require_once (ME_PATH . 'includes/class-me-hft-builder.php');
require_once (ME_PATH . 'includes/helper-functions.php');

require_once (plugin_dir_path(__FILE__) . 'admin/admin-functions.php' ); 



add_filter( 'woocommerce_locate_template', 'custom_woocommerce_template', 1, 3 );
   function custom_woocommerce_template( $template, $template_name, $template_path ) {
     global $woocommerce;
     $_template = $template;
     if ( ! $template_path ) 
        $template_path = $woocommerce->template_url;
 
     $plugin_path  = untrailingslashit( dirname( __FILE__ ) )  . '/woocommerce/';
 
    // Look within passed path within the theme - this is priority
    $template = locate_template(
    array(
      $template_path . $template_name,
      $template_name
    )
   );
 
   if( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;
 
   if ( ! $template )
    $template = $_template;

   return $template;
}



// Override content-single-product.php
add_filter( 'wc_get_template_part', 'custom_woocommerce_templates', 10, 3 );
function custom_woocommerce_templates( $template, $slug, $name ){
	if ( $slug == 'content' && $name == 'single-product')  {

		if( locate_template('content-single-product.php') ) {
			$template = locate_template( $file_name );
		} else {
			// Template not found in theme's folder, use plugin's template as a fallback
			$template = dirname( __FILE__ ) . '/woocommerce/content-single-product.php';
		}

	}
	return $template;
}




add_filter( 'template_include', 'woocommerce_archive_template', 99 );
function woocommerce_archive_template( $template ) {
	if( function_exists('is_woocommerce')) { 
		if ( is_woocommerce() && is_archive() ) {
			$new_template = plugin_dir_path( __FILE__ ) . '/woocommerce/archive-product.php';
			if ( !empty( $new_template ) ) {
				return $new_template;
			}
		}
	}

    return $template;
}






// add js to single product shop page
function my_scripts_method() {
	if(is_product()){
		wp_enqueue_script(
			'custom-script',
			ME_ASSETS_URL . '/js/single-product-page.js',
			array('jquery')
		);?>
		<?php if(get_option('configurator-template-kits-blocks')['display_breadcrumb-option'][0]=='hide'){ ?>
			<style>
				.single-post-breadcrumb{display:none;}
			</style>
		<?php }
		
	}
}
	



 
// front elements style

function front_elements_style() {
		wp_enqueue_style( 'front-elements', ME_ASSETS_URL.'/css/front-elements.css' );    
    // wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'front_elements_style' );




// add action

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

$configurator_kit    = is_plugin_active('configurator-template-kits-blocks-pro/configurator-template-kits-blocks-pro.php');

if(!$configurator_kit ){
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'ME_action_links' );
}

function ME_action_links( $actions ) {
   $actions[] = '<a href="https://configurator.wpconfigurator.com" target="_blank" style="color:red;font-weight:900;">Go Pro</a>';
   return $actions;
}


 


//add_action('admin_head', 'hide_admin_footer');

function hide_admin_footer() {
	if($_GET['page']=='configurator-elements'){
		echo '<style>
		#wpfooter {
			display: none;
		} 
		</style>';
	}
	
  
}