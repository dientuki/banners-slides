<?php
abstract class bannersCore{
	
	/**
	 * Plugin's url
	 */	
	protected $plugin_url = null;
	
	/**
	 * Plugin's dir
	 */	
	protected $plugin_dir = null;	
	
	/**
	 * Current theme dir
	 */	
	protected $theme_dir = null;
	
	/**
	 * Banner's filename
	 */	
	protected $banners_filename = 'banner.html';
	
	/**
	 * Custom js' filename
	 */	
	protected $custom_js_filename = 'custom.js';	

	/**
	 * Use config
	 */		
	protected $use = null;
	
	/**
	 * Basic config
	 */		
	protected $basic = null;
	
	/**
	 * Custom js config
	 */		
	protected $custom_js = null;
	
	/**
	 * Img size
	 */		
	protected $size = null;	
	
	/**
	 * The wp upload dir
	 */		
	protected $upload_dir = null;	
	
	/**
	 * The options' name
	 */	
	protected $opt_use = 'aquit_banner_use';
	protected $opt_custom_js = 'aquit_banner_custom_js';
	protected $opt_img_size = 'aquit_banner_size';
	protected $opt_basic = 'aquit_banner_basic';
	
	/**
	 * Table name
	 */		
	protected $wp_table = 'aquit_banners';
	
	/**
	 * The banner upload dir
	 */		
	protected $upload_banner_dir = null;
	
	/**
	 * The banner upload url
	 */		
	protected $upload_banner_url = null;
	
	/**
	 * The constructor
	 *
	 * @return void 
	 */		
	function __construct() {
		
		//used by the frontend
		$this->plugin_url = plugin_dir_url(dirname(__FILE__));
		$this->plugin_dir = WP_PLUGIN_DIR . '/' . plugin_basename(dirname(dirname(__FILE__)));
		$this->theme_dir = $this->get_theme_dir();
		$this->custom_js_filename = 'plugin/' . $this->custom_js_filename;
		
		//options to use
		$this->upload_dir = $this->get_upload_dir();
		$this->upload_banner_dir = ABSPATH . $this->upload_dir . '/banners/';
		$this->upload_banner_url = get_option('siteurl') . '/' . $this->upload_dir . '/banners/';	
	}
	
	/**
	 * Get upload dir
	 * 
	 * @return string
	 */
	private function get_upload_dir(){
		$upload_dir = get_option('upload_path');
		
		if ( empty($upload_path) ) {
			return 'wp-content/uploads';
		}
		return $upload_dir;
	}
	
	/**
	 * Get theme dir
	 * 
	 * @return string
	 */
	private function get_theme_dir(){
		$theme = get_theme(get_option('current_theme'));
		return $theme['Template Dir'] . '/';
	}

	/**
	 * Get upload banner url
	 * 
	 * @return string $upload_banner_url
	 */	
	public function get_upload_banner_url(){
		return $this->upload_banner_url;
	}
	
	/**
	 * Get upload banner dir
	 * 
	 * @return string $upload_banner_dir
	 */	
	public function get_upload_banner_dir(){
		return $this->upload_banner_dir;
	}

	/**
	 * Get plugin dir
	 * 
	 * @return string $plugin_dir
	 */	
	public function get_plugin_dir(){
		return $this->plugin_dir;
	}
	
	/**
	 * Delete a file
	 * 
	 * @return void
	 */		
	protected function delete_file($file = null){
		if ($file != null){
			if (file_exists($file) && is_file($file)) {
				chmod($file, 0777);
				unlink($file);
			}
		}
	}	
	
	/**
	 * Delete html file
	 * 
	 * @return void
	 */		
	protected function delete_html(){
		
		$tmp = $this->theme_dir . $this->banners_filename;

		if (file_exists($tmp)){
			$this->delete_file($tmp);	
		} else {
			$tmp = $this->plugin_dir . '/plugin/' . $this->banners_filename;
			if (file_exists($tmp)){
				$this->delete_file($tmp);
			}			
		}	
	}	
}