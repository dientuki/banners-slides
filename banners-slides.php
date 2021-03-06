<?php
/*
Plugin Name: Banners Slides
Plugin URI: http://www.senpai.com.ar/banners-slides
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

function check_wpcodev_basli($name){
	if (! is_wpcodev()) {
		echo '<div id="message" class="error">Banners Slides need WP-Codev installed and active to work</div>';
	}
}

/**
 * Activate functions
 * 
 * @return void
 */
function basli_activate(){
	include_once dirname(__FILE__) . '/class/basliInstall.php';
	$install = new basliInstall();
	$install->activate();
	unset($install);
}

/**
 * Deactivate funcionts
 * 
 * @return void
 */
function basli_deactivate(){
	include_once dirname(__FILE__) . '/class/basliInstall.php';
	$install = new basliInstall();
	$install->deactivate();
	unset($install);
}

/**
 * Add the main menu
 * @return void
 */
function basli_menu() {
	
	$icon = plugin_dir_url(__FILE__) . 'icon-16.png';
	
	$p = 'banners-slides/';
	
	$pages = array(
		array('file' => $p . 'admin/banners.php', 'label' => 'Banners'),
		array('file' => $p . 'admin/images.php', 'label' => 'Images'),
		array('file' => $p . 'admin/animation.php', 'label' => 'Animation'),
		array('file' => $p . 'admin/full-config.php', 'label' => 'Configuration')
	);	

	add_menu_page('Banners Slides', 'Banners Slides', 'manage_options', $pages[0]['file'], '', $icon );
	foreach ($pages as $page){
		add_submenu_page( $pages[0]['file'], $page['label'], $page['label'], 'manage_options', $page['file']);	
	}
}

/**
 * Do all the magic
 */
include_once dirname(__FILE__) . '/class/basliCore.php';

if (is_wpcodev()) {
	if (is_admin()) {
		add_action('admin_menu', 'basli_menu');
		wp_enqueue_style( 'basli', plugin_dir_url(__FILE__).'admin/admin.css', null, null);
	} else {
		include_once dirname(__FILE__) . '/class/basliFrontend.php';
		$frontend = new basliFrontend();
		$frontend->add_css();
	}
}

/**
 * call basli.html and include it
 * 
 * @return void
 */
function show_banner(){
	include_once dirname(__FILE__) . '/class/basliFrontend.php';
	$frontend = new basliFrontend();
	$frontend->show_banner();
}

//register_activation_hook(__FILE__ , 'banners_activate' );
//register_deactivation_hook(__FILE__, 'banners_deactivate');
add_action('admin_notices', 'check_wpcodev_basli');