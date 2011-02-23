<?php
class bannersImages extends bannersBackend {

	private $wpdb = null;
	
	private $fields = array();
	
	function __construct($tab = null){
		global $wpdb;
		
		parent::__construct($tab);

		$this->wpdb = $wpdb;
		$this->wp_table = $this->wpdb->prefix . $this->wp_table;
		$this->fields = array(
							'id' => '',
							'order' => '',
							'image' => '',
							'alt' => '',
							'link' => '',
							'show' => '');
	}
	
	public function set_id($value){
		$this->fields['id'] = $value;
	}

	public function set_order($value){
		$this->fields['order'] = $value;
	}
	
	public function set_image($value){
		$this->fields['image'] = $value;
	}
	
	public function set_alt($value){
		$this->fields['alt'] = $value;
	}
	
	public function set_link($value){
		$this->fields['link'] = $value;
	}

	public function set_show($value){
		$this->fields['show'] = $value;
	}
		
	public function make_html(){
		$this->write_html($this->get_html());
	}	
	
	private function get_html(){
		
		$sql = 'SELECT id, `order`, image, alt, link FROM ' . $this->wp_table . ' WHERE `show` = 1 ORDER BY `order` ASC';
		$images = $this->wpdb->get_results($sql);
		
		if (count($images) == 0){
			//delete, just in case
			$this->delete_html();
			return false;
		}
		
		$html = '';
		$html .= '<div id="banner" class="slider_content">' . "\n";
		$html .= '<div id="slideshow" class="slider_full">'  . "\n";
		$first = true;
		
		foreach($images as $image){
			$div = null;
			$img = null;
			
			if ($first != true) {
				$div = '<div class="slider_item">';	
			} else {
				$div = '<div class="slider_item first">';
			}
			
			$img = '<img src="' . $this->upload_banner_url . $image->image . '" width="'. $this->size['w'] . '" height="'. $this->size['h'] . '" alt="'. $image->alt . '" title="'. $image->alt . '" />'; 
			
			if ($image->link != '') {
				$img = '<a href="'.$image->link.'" title="'. $image->alt . '">' . $img . '</a>' ;
			}
					
			$html .= $div . $img . "</div>\n";
			
			$first = false;
		}
		$html .= "</div>\n"; //close #slideshow
		/*
		if ($this->custom_js['pager'] === 1 ) {
			$html .= '<div id="slider-nav" class="slider_nav"></div>' . "\n";
		}
		*/
		$html .= "</div>\n";//close #banner
		
		return $html;
	}
	
	private function write_html($html = false){
		
		if ($html == false){
			return false;
		}
		
		$file = 'banner.html';
		
		$tmp = $this->theme_dir . 'banner.html';	
		
		//let make the html in the theme
		if (!($file_html = fopen($tmp, 'w+'))) {

			//we can't make the html in the theme, let's try to do it in the plugin
			$tmp =  $this->plugin_dir . '/plugin/' . $file;
			
			if (!($file_html = fopen($tmp, 'w+'))) {
				//we can't make the file in the plugin, we are in trouble
				die('<p class="error">fuck</p>');
			}
		}
		fwrite($file_html, $html);
		fclose($file_html);		
	}
	
	private function sanitize_fields($value){
		return '`' . $value . '`';
	}
	
	private function sanitize_values($value){
		return '\'' . $value . '\'';
	}
	
	public function sanitize_filename($filename){
		$filename = preg_replace('/\s+/', '-', $filename);
		$filename = preg_replace('/[^a-zA-Z0-9.-]/', '', $filename);
		return $filename;
	}
	
	public function insert(){
		foreach ($this->fields as $field => $value){
			if (empty($value)){
				unset($this->fields[$field]);
			}
		}
		$fields = implode(array_map(array($this, 'sanitize_fields'), array_keys($this->fields)), ',');
		$values = implode(array_map(array($this, 'sanitize_values'), $this->fields), ',');

		$sql = 'INSERT INTO ' . $this->wp_table . ' (' . $fields . ') VALUES ('. $values. ');';
		$this->wpdb->query($sql);
	}
	
	public function update(){
		$sql = ' WHERE id = ' . $this->fields['id'];
		unset($this->fields['id']);

		foreach ($this->fields as $field => $value){
			if (!empty($value)){
				$tmp[] = '`' . $field . '` = \'' . $value . '\'';
			}
		}
		
		$tmp = implode($tmp, ',');

		$sql = 'UPDATE ' . $this->wp_table . ' SET ' . $tmp . $sql;
		$this->wpdb->query($sql);
	}

	public function delete_image($id){
		if ($id == null){
			return false;
		}
		
		$image_old = $this->get_image($id);
		$this->delete_file($this->upload_banner_dir . 'original/' . $image_old['image']);
		$this->delete_file($this->upload_banner_dir . $image_old['image']);
		return true;		
	}
	
	public function get_images(){
		
		$sql = 'SELECT id, `order`, image, alt, link, `show` FROM ' . $this->wp_table . ' ORDER BY `order` ASC';
		$images = $this->wpdb->get_results($sql);

		return $images;
	}
	
	public function get_image($id){
		if ($id == null){
			return false;
		}
		
		$sql = 'SELECT id, `order`, image, alt, link, `show` FROM ' . $this->wp_table . ' WHERE id = ' . $id . ' LIMIT 1';
		return $this->wpdb->get_row($sql, ARRAY_A);
	}
	
	private function delete_row($id){
		if ($id == null){
			return false;
		}
		
		$sql = 'DELETE FROM ' . $this->wp_table . ' WHERE id = ' . $id . ' LIMIT 1';
		$this->wpdb->query($sql);		
	}
	
	public function delete($id = null){
		if ($id == null){
			return false;
		}
		
		$this->delete_image($id);
		$this->delete_row($id);
	}
	
	public function rename_image($filename, $count = 1){
		
		// Replace spaces from the filename
		$filename = preg_replace('/\s+/', '-', $filename);
		
		// Sanitize filename
		$filename = preg_replace('/[^a-zA-Z0-9.-]/', '', $filename);
		
		$destination = $this->get_upload_banner_dir(). 'original/' . $filename;
		
		if (file_exists($destination)){
			$name = substr($filename, 0, strripos($filename, '.'));
			$ext = substr($filename, strripos($filename, '.'));
			
			if ($count == 1){
				$name .= '-1';
			} else {
				$name = substr($filename, 0, strripos($filename, '-') + 1);
				$name .= $count;
			}
			$filename = $name . $ext;
			$count++;
			
			$filename = $this->rename_image($filename, $count);
		}
		return $filename;	
	}
	
}