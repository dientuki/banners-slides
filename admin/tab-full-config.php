<?php 
$backend = new bannersBackend($page);

if (array_key_exists('config', $_POST)) {
	switch ($_POST['config']){
		case 'basic':
			$backend->set_basic('resize', $_POST['resize'] == 1 ? 1 : 0);
			$backend->set_basic('crop', $_POST['crop'] == 1 ? 1 : 0);
			$backend->set_basic('overwrite', $_POST['overwrite'] == 1 ? 1 : 0);
			$backend->set_basic('delete', $_POST['delete'] == 1 ? 1 : 0);
			$backend->update_basic();
					
			break;
		case 'advanced':
			$backend->set_use('css', $_POST['css'] == 1 ? 1 : 0);
			$backend->set_use('js', $_POST['js'] == 1 ? 1 : 0);
			$backend->set_use('cycle', $_POST['cycle'] == 1 ? 1 : 0);
			$backend->update_use();
			
			break;
	}
}
?>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<h2>Basic configuration</h2>
	<h3>Uploading</h3>
	<p>Resize an image when is uploading?</p>
	<p>
		<label for="resize_yes"><input type="radio" id="resize_yes" name="resize" value="1" <?php if ($backend->get_basic('resize') == 1): ?>checked="checked"<?php endif; ?> /> Yes</label>
		<label for="resize_no"><input type="radio" id="resize_no" name="resize" value="0" <?php if ($backend->get_basic('resize') == 0): ?>checked="checked"<?php endif; ?> /> No</label>
	</p>
	<p>Crop image to exact dimension or resize?</p>
	<p>
		<label for="crop_yes"><input type="radio" id="crop_yes" name="crop" value="1" <?php if ($backend->get_basic('crop') == 1): ?>checked="checked"<?php endif; ?> /> Crop</label>
		<label for="crop_no"><input type="radio" id="crop_no" name="crop" value="0" <?php if ($backend->get_basic('crop') == 0): ?>checked="checked"<?php endif; ?> /> Resize</label>
	</p>	
	<!-- 
	<p>Overwrite?</p>
	<p>
		<label for="overwrite_yes"><input type="radio" id="overwrite_yes" name="overwrite" value="1" <?php if ($backend->get_basic('overwrite') == 1): ?>checked="checked"<?php endif; ?> /> Yes</label>
		<label for="overwrite_no"><input type="radio" id="overwrite_no" name="overwrite" value="0" <?php if ($backend->get_basic('overwrite') == 0): ?>checked="checked"<?php endif; ?> /> No</label>
	</p>
	 -->
	<h3>Plugin data</h3>
	<p>Delete all my data (configurations, images, etc) when disabling plugin</p>
	<p>
		<label for="delete_yes"><input type="radio" id="delete_yes" name="delete" value="1" <?php if ($backend->get_basic('delete') == 1): ?>checked="checked"<?php endif; ?> /> Yes</label>
		<label for="delete_no"><input type="radio" id="delete_no" name="delete" value="0" <?php if ($backend->get_basic('delete') == 0): ?>checked="checked"<?php endif; ?> /> No</label>
	</p>
	<input type="hidden" value="basic" name="config">
	<div class="submit"><input type="submit" name="action" value="Update Settings" /></div>
</form>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<h2>Advanced configuration</h2>
	<p class="error">You don't need to touch here to make this shit work but do it if your shit works better</p>
	<h3>Use the styles from the plugin or from your theme?</h3>
	<p>Use from your theme if you have you code optimezed</p>
	<p>
		<label for="css_plugin"><input type="radio" id="css_plugin" name="css" value="1" <?php if ($backend->get_use('css') == 1): ?>checked="checked"<?php endif; ?> /> Plugin</label>
		<label for="css_theme"><input type="radio" id="css_theme" name="css" value="0" <?php if ($backend->get_use('css') == 0): ?>checked="checked"<?php endif; ?> /> Theme</label>
	</p>
	<h3>Use Cicle from the plugin or from your theme?</h3>
	<p>Use from your theme if you have you code optimezed</p>
	<p>
		<label for="cycle_plugin"><input type="radio" id="cycle_plugin" name="cycle" value="1" <?php if ($backend->get_use('cycle') == 1): ?>checked="checked"<?php endif; ?> /> Plugin</label>
		<label for="cycle_theme"><input type="radio" id="cycle_theme" name="cycle" value="0" <?php if ($backend->get_use('cycle') == 0): ?>checked="checked"<?php endif; ?> /> Theme</label>
	</p>	
	<h3>Use custom javascript from the plugin or from your theme?</h3>
	<p>Use from your theme if you have you code optimezed</p>
	<p>
		<label for="js_plugin"><input type="radio" id="js_plugin" name="js" value="1" <?php if ($backend->get_use('js') == 1): ?>checked="checked"<?php endif; ?> /> Plugin</label>
		<label for="js_theme"><input type="radio" id="js_theme" name="js" value="0" <?php if ($backend->get_use('js') == 0): ?>checked="checked"<?php endif; ?> /> Theme</label>
	</p>
	<input type="hidden" value="advanced" name="config">
	<div class="submit"><input type="submit" name="action" value="Update Settings" /></div>
</form>