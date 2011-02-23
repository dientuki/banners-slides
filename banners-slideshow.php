<?php
/*
Plugin Name: Banners Slideshow
Plugin URI: http://www.senpai.com.ar/banners-slideshow
Description: A fast and powerfull implemetation of jQuery's Cycle with wp-codev
Author: Dientuki
Author URI: http://www.dientuki.com.ar 
Version: 0.10
*/

if (!function_exists('is_wpcodev')) {
	function is_wpcodev(){

		$is_wpcodev = in_array( 'wp-codev/wp-codev.php', (array) get_option( 'active_plugins', array() ) );
		
		if ( $is_wpcodev == false) {
			//Plugin is not active, let's check in the network
			if ( is_multisite() ) {
				$plugins = get_site_option( 'active_sitewide_plugins');
				if ( isset($plugins['wp-codev/wp-codev.php']) ) {
					//Plugin is active in the network
					return true;
				}				
			}
			
			//Plugin is not active, fuck
			return false;	
		}
		//Plugin is active
		return true;
	}
}

function check_wpcodev_banners($name){
	if (! is_wpcodev()) {
		echo '<div id="message" class="error">Banners need WP-Codev installed and active to work</div>';
	}
}

/**
 * Activate functions
 * 
 * @return void
 */
function banners_activate(){
	include_once dirname(__FILE__) . '/class/bannersInstall.php';
	$install = new bannersInstall();
	$install->activate();
	unset($install);
}

/**
 * Deactivate funcionts
 * 
 * @return void
 */
function banners_deactivate(){
	include_once dirname(__FILE__) . '/class/bannersInstall.php';
	$install = new bannersInstall();
	$install->deactivate();
	unset($install);
}

/**
 * Add the main menu
 * @return void
 */
function banners_menu() {
	global $aquit_o;
	
	$icon = plugin_dir_url(__FILE__) . 'icon-16.png';

	$p = 'banners';
	if (isset($aquit_o) == false) {
		$aquit_o = $p . '/admin/admin.php';
		add_menu_page('Banners Slides', 'Banners Slides', 'manage_options', $aquit_o, '', $icon );
		add_submenu_page( $aquit_o, 'Banners Slides', 'Banners Slides', 'manage_options', $aquit_o);
	} else {
		add_submenu_page( $aquit_o, 'Banners Slides', 'Banners Slides', 'manage_options', $p . '/admin/admin.php');
	}
}

/**
 * Do all the magic
 */
include_once dirname(__FILE__) . '/class/bannersCore.php';

if (is_wpcodev()) {
	if (is_admin()) {
		add_action('admin_menu', 'banners_menu');
		wp_enqueue_style( 'banner-aquit', plugin_dir_url(__FILE__).'admin/admin.css', null, null);
	} else {
		include_once dirname(__FILE__) . '/class/bannersFrontend.php';
		$frontend = new bannersFrontend();
		$frontend->add_css();
	}
}

/**
 * call banner.html and include it
 * 
 * @return void
 */
function show_banner(){
	include_once dirname(__FILE__) . '/class/bannersFrontend.php';
	$frontend = new bannersFrontend();
	$frontend->show_banner();
}

//register_activation_hook(__FILE__ , 'banners_activate' );
//register_deactivation_hook(__FILE__, 'banners_deactivate');
add_action('admin_notices', 'check_wpcodev_banners');