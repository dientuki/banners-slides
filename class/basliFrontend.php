<?php
class basliFrontend extends basliCore{
	
	/**
	 * The constructor
	 *
	 * @return void 
	 */		
	function __construct(){
		parent::__construct();
		$this->use = unserialize(get_option($this->opt_use));
		$this->basic = unserialize(get_option($this->opt_basic));
	}
	
	/**
	 * Include a file
	 * 
	 * @return boolean
	 */	
	private function include_file($file = null){
		
		if ($file == null) {
			return false;
		}
		
		//Let's find the file in the theme
		$tmp = $this->theme_dir . $file;
		if (file_exists($tmp)){
			include_once($tmp);
			return true;
		}
		
		//Let's find the file in the plugin
		$tmp = $this->plugin_dir . '/plugin/' . $file;
		if (file_exists($tmp)){
			include_once($tmp);
			return true;
		}

		return false;
	}
	
	/**
	 * Add the js to the footer
	 * 
	 * @return void
	 */
	public function add_js(){

		if ($this->use['cycle'] == 1) {
			$cycle = $this->plugin_url . 'plugin/jquery.' . $this->basic['cycle'] . '.min.js';		
			echo '<script type="text/javascript" src="'. $cycle .'"></script>' . "\n";
		}
		if ($this->use['js'] == 1){
			$custom = $this->plugin_url . $this->custom_js_filename;
			echo '<script type="text/javascript" src="'. $custom .'"></script>' . "\n";
		}
	}
	
	/**
	 * enquenue the css to the header
	 * 
	 * @return void
	 */	
	public function add_css(){

		if ($this->use['css'] == 1){
			wp_enqueue_style( 'banner-aquit', $this->plugin_url.'plugin/jquery.cycle.css', null, null);
		}
			
	}
	
	/**
	* Include the html in the theme
	* 
	* @return void
	*/	
	public function show_banner(){
		
		if ($this->include_file($this->banners_filename) == true){
			add_action('wp_footer', array($this, 'add_js'));
		} else {
			if (is_user_logged_in()) {
				echo '<div>The banner.html does exist, even in the pluging folder!</div>';
			}			
		}
	}
}
?>