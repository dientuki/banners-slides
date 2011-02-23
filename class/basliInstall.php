<?php
class basliInstall extends basliCore{	
	
	/**
	 * The wpdb object
	 */		
	private $wpdb = null;
	
	/**
	 * The constructor
	 *
	 * @return void 
	 */		
	function __construct() {
		global $wpdb;
		
		parent::__construct();
				
		$this->wpdb = $wpdb;
		$this->wp_table = $this->wpdb->prefix . $this->wp_table;		
	}

	/**
	 * Activation function
	 * 
	 * @return void
	 */		
	public function activate(){
		
		$this->basic = unserialize(get_option($this->opt_basic));
		if (empty($this->basic)){

			$this->create_table();
			
			$this->create_folders();
			
			$this->permission();
			
			$this->init();
	
			$this->add_options();
		}
	}
	
	/**
	 * Deactivation function
	 * 
	 * @return void
	 */		
	public function deactivate(){
		$this->basic = unserialize(get_option($this->opt_basic));
		if ($this->basic['delete'] == 1){

			$this->drop_table();

			$this->delete_folders();

			$this->delete_options();
			
			$this->delete_html();
			
			$this->delete_js();

		}
	}

	/**
	 * Create table in db
	 * 
	 * @return void
	 */		
	private function create_table(){
		$sql = 'CREATE TABLE ' . $this->wp_table . ' (
				id INT NOT NULL AUTO_INCREMENT,
				`order` INT NOT NULL DEFAULT 0,
				image VARCHAR( 255 ) NOT NULL,
				alt VARCHAR( 255 ) NOT NULL,
				link VARCHAR( 255 ) NOT NULL,
				`show` BOOLEAN NOT NULL,
			PRIMARY KEY (id),
			UNIQUE (id)
		)';
		$this->wpdb->query($sql);		
	}
	
	/**
	 * Drop table in db
	 * 
	 * @return void
	 */		
	private function drop_table(){
		$this->wpdb->query('DROP TABLE ' . $this->wp_table);
	}
	
	/**
	 * Create uploads folders
	 * 
	 * @return void
	 */		
	private function create_folders(){
		mkdir($this->upload_banner_dir);
		chmod($this->upload_banner_dir, 0777);
		
		mkdir($this->upload_banner_dir . '/original');
		chmod($this->upload_banner_dir . '/original', 0777);
	}
	
	/**
	 * Permission folders
	 * 
	 * @return void
	 */		
	private function permission(){
		//mkdir($this->upload_banner_dir);
		chmod($this->plugin_dir . '/plugin', 0777);
	}	
	
	/**
	 * Delete uploads folders
	 * 
	 * @return void
	 */		
	private function delTree($dir) {
	    $files = glob( $dir . '*', GLOB_MARK );
	    foreach( $files as $file ){
	        if( substr( $file, -1 ) == '/' )
	            $this->delTree( $file );
	        else
	            unlink( $file );
	    }
	    rmdir( $dir );
	} 	
	
	/**
	 * Delete uploads folders
	 * 
	 * @return void
	 */		
	private function delete_folders(){
		chmod($this->upload_banner_dir, 0777);
		$this->delTree($this->upload_banner_dir);		
	}
	
	/**
	 * Init options
	 * 
	 * @return void
	 */		
	private function init(){
		$this->use['js'] = 1;
		$this->use['css'] = 1;
		$this->use['cycle'] = 1;	

		$this->size['w'] = 750;
		$this->size['h'] = 250;

		$this->custom_js['timeout'] = 4000;
		$this->custom_js['speed'] = 1000;
		$this->custom_js['pause'] = 0;
		$this->custom_js['pager'] = 0;
		$this->custom_js['pagerAnchorBuilder'] = 'blank';	

		$this->basic['resize'] = 1;
		$this->basic['crop'] = 1;
		$this->basic['overwrite'] = 0;
		$this->basic['delete'] = 1;
		$this->basic['cycle'] = 'cycle.lite';		
	}
	
	/**
	 * Add options to the db
	 * 
	 * @return void
	 */		
	private function add_options(){
		add_option($this->opt_use, serialize($this->use), '', 'no');
		add_option($this->opt_img_size, serialize($this->size), '', 'no');
		add_option($this->opt_custom_js, serialize($this->custom_js), '', 'no');
		add_option($this->opt_basic, serialize($this->basic), '', 'no');		
	}
	
	/**
	 * Delete options from the db
	 * 
	 * @return void
	 */		
	private function delete_options(){
		delete_option($this->opt_use);
		delete_option($this->opt_img_size);
		delete_option($this->opt_custom_js);
		delete_option($this->opt_basic);		
	}
	
	/**
	 * Delete js from the plugin
	 * 
	 * @return void
	 */		
	private function delete_js(){
		$file = $this->plugin_dir . '/' . $this->custom_js_filename;

		if (file_exists($file)){
			$this->delete_file($file);	
		}		
	}
}