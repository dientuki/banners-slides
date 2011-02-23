<?php
//must check that the user has the required capability 
if (!current_user_can('manage_options')) {
	wp_die( __('You do not have sufficient permissions to access this page.') );
}
/*
 * Create settings page tabs 
 */
function banner_settings_tabs() {
	$default_tabs = array(
		'banners' => 'Banners',
		'images' => 'Images',
		'animation' => 'Animation',
		'full-config' => 'Configuration'
	);

	return apply_filters('banner_settings_tabs', $default_tabs);
}

if(isset($_GET['tab'])){
	$page = $_GET['tab'];
}else{
	$page = 'banners';
}

include_once dirname(dirname(__FILE__)) . '/class/bannersBackend.php';
include_once(dirname(dirname(__FILE__)) . '/tools/aqt_wpfw.php');
?>
<div id="banners_options" class="wrap">
	<div id="banners_settings_nav_bar">
		<ul id="sidemenu" >
			<?php
				$tabs = banner_settings_tabs(); 
				foreach ( $tabs as $callback => $text ) {
					$class = '';
					if ( $page == $callback ) {
						$class = " class='current'";
						}
					$href = add_query_arg(array('tab'=>$callback, 's'=>false, 'paged'=>false, 'post_mime_type'=>false, 'm'=>false));
					$href = remove_query_arg('isocode', $href);
					$href = wp_nonce_url($href, "tab-$callback");
					$link = "<a href='" . clean_url($href) . "'$class>$text</a>";
					echo "\t<li id='" . attribute_escape("tab-$callback") . "'>$link</li>\n";
				}
			?>
		</ul>
	</div>
	<div style='clear:both;'></div>
	<div id='banners_options_page'>
		<?php $admin = dirname(__FILE__);?>
		<?php require_once($admin . '/tab-'. $page  . '.php');?>
	</div>	
</div>