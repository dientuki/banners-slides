<?php
class bannersBackend extends bannersCore{
	
	protected $error = null;
	
	function __construct($tab = null) {
		parent::__construct();
		
		switch ($tab){
			case 'banners':
				$this->basic = unserialize(get_option($this->opt_basic));
				$this->size = unserialize(get_option($this->opt_img_size));			
				break;
			case 'images':
				$this->use = unserialize(get_option($this->opt_use));
				$this->size = unserialize(get_option($this->opt_img_size));	
				break;
			case 'animation':
				$this->use = unserialize(get_option($this->opt_use));
				$this->custom_js = unserialize(get_option($this->opt_custom_js));
				$this->basic = unserialize(get_option($this->opt_basic));	
				break;
			case 'full-config':
				$this->use = unserialize(get_option($this->opt_use));
				$this->basic = unserialize(get_option($this->opt_basic));				
				break;												
		}
		
		wp_enqueue_style( 'banner-aquit', $this->plugin_url.'admin/admin.css', null, null);
	}
	
	public function get_error(){
		return $this->error;
	}
	
	public function set_error($value){
		$this->error = $value;
	}	
	
	/**
	 * get value
	 * 
	 * @param string $param Parameter
	 * @return array or string
	 */		
	public function get_size($param = null){
		if ($param == null) {
			return $this->size;
		} else {
			if (array_key_exists($param, $this->size)){
				return $this->size[$param];
			}
		}
		return false;
	}
	
	/**
	 * Set new value
	 * 
	 * @param string $param Parameter
	 * @param string $value Value
	 * @return void
	 */	
	public function set_size($param, $value){
		if (array_key_exists($param, $this->size)){
			$this->size[$param] = $value;
		}
	}
	
	/**
	 * update
	 * 
	 * @return void
	 */		
	public function update_size(){
		update_option($this->opt_img_size, serialize($this->size));
	}
	
	/**
	 * get value
	 * 
	 * @param string $param Parameter
	 * @return array or string
	 */		
	public function get_use($param = null){
		if ($param == null) {
			return $this->use;
		} else {
			if (array_key_exists($param, $this->use)){
				return $this->use[$param];
			}
		}
		return false;
	}
	
	/**
	 * Set new value
	 * 
	 * @param string $param Parameter
	 * @param string $value Value
	 * @return void
	 */	
	public function set_use($param, $value){
		if (array_key_exists($param, $this->use)){
			$this->use[$param] = $value;
		}
	}
	
	/**
	 * update
	 * 
	 * @return void
	 */		
	public function update_use(){
		update_option($this->opt_use, serialize($this->use));
	}
	
	/**
	 * get value
	 * 
	 * @param string $param Parameter
	 * @return array or string
	 */		
	public function get_custom_js($param = null){
		if ($param == null) {
			return $this->custom_js;
		} else {
			if (array_key_exists($param, $this->custom_js)){
				return $this->custom_js[$param];
			}
		}
		return false;
	}
	
	/**
	 * Set new value
	 * 
	 * @param string $param Parameter
	 * @param string $value Value
	 * @return void
	 */	
	public function set_custom_js($param, $value){
		if (array_key_exists($param, $this->custom_js)){
			$this->custom_js[$param] = $value;
		}
	}
	
	/**
	 * update
	 * 
	 * @return void
	 */		
	public function update_custom_js(){
		update_option($this->opt_custom_js, serialize($this->custom_js));
	}	
	
	/**
	 * get value
	 * 
	 * @param string $param Parameter
	 * @return array or string
	 */		
	public function get_basic($param = null){
		if ($param == null) {
			return $this->basic;
		} else {
			if (array_key_exists($param, $this->basic)){
				return $this->basic[$param];
			}
		}
		return false;
	}
	
	/**
	 * Set new value
	 * 
	 * @param string $param Parameter
	 * @param string $value Value
	 * @return void
	 */	
	public function set_basic($param, $value){
		if (array_key_exists($param, $this->basic)){
			$this->basic[$param] = $value;
		}
	}
	
	/**
	 * update
	 * 
	 * @return void
	 */		
	public function update_basic(){
		update_option($this->opt_basic, serialize($this->basic));
	}	
			
}
?>