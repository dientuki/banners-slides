<?php
class basliAnimation extends basliBackend {

	/**
	 * The constructor
	 *
	 * @return void 
	 */		
	function __construct($tab = null){
		parent::__construct($tab);
	}
	
	/**
	 * Make and return the custon js
	 * 
	 * @return string $javascript
	 */	
	private function get_js(){
		$custom = $this->custom_js;
		$pager = '$(\'#slideshow\').after(\'<div id="slider-nav" class="slider_nav"></div>\');';
		
		if ($custom['pager'] == 1){
			$custom['pager'] = "'#slider-nav'";
			$custom['pagerAnchorBuilder'] = 'paginate';
			
			switch ($this->custom_js['pagerAnchorBuilder']){
				case 'blank':
					$paginate = '\'<a href="#"></a>\'';
					break;
				case 'numbers':
					$paginate = '\'<a href="#">\' + (ind + 1) + \'</a>\'';
					break;
				case 'letters':
					$paginate = '\'<a href="#">\' + String.fromCharCode(65 + ind) + \'</a>\'';
					break;
			}
			$paginate = 'function paginate(ind, el){ return ' . $paginate . ';}';
			
		} else {
			unset($custom['pager']);
			unset($custom['pagerAnchorBuilder']);
		}
		
		if ($custom['pause'] == 1){
			$custom['pause'] = 'true';
		}
		
		foreach ($custom as $param => $value){
			$js[] = $param . ': ' . $value;
		}
		
		$js = "$('#slideshow').cycle({" . implode(', ', $js) . '});';
		if (isset($custom['pager'])){
			$js = $pager . "\n" . $js;
		}
		if (isset($custom['pagerAnchorBuilder'])){
			$js = $paginate . "\n" .  $js;
		}
		
		unset($custom);
		
		return '$(document).ready(function(){' . "\n" . $js .  "\n" . '});';		
	}
	
	/**
	 * Write custom js
	 * 
	 * @param string $js javascritp to write
	 * @return void
	 */	
	private function write_js($js){
		$file = $this->plugin_dir . '/' .$this->custom_js_filename;
		
		if (!($file = fopen($file, 'w+'))) {
			//we can't make the file in the plugin, we are in trouble
			$this->error = 'We can\'t write the javascript in the plugin';
			return false;
		}
		
		fwrite($file, $js);
		fclose($file);
		return true;
	}	
	
	public function make_js(){
		$this->write_js($this->get_js());
	}	
}