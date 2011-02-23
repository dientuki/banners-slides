<?php 
$backend = new bannersBackend($page);
$form = new wpfw_form(get_bloginfo('charset'));

$post = array(
	'width' => $backend->get_size('w'),
	'height' => $backend->get_size('h')
);

if (array_key_exists('action', $_POST)) {

	unset($_POST['action']);
	
	$post = new validation($_POST);
	
	$post->rule('width', 'not_empty');
	$post->rule('width', 'digit');
	$post->rule('height', 'not_empty');
	$post->rule('height', 'digit');	
	
	if ($post->check()){
		$backend->set_size('w', $_POST['width']);
		$backend->set_size('h', $_POST['height']);
	
		$backend->update_size();
		
		//debo volver a cropear
		
	} else {
		$errors = $post->errors();
	}
}
?>

<?php echo $form->open(); ?>
	<h2>Images</h2>
	<?php if ($backend->get_use('css') == 0):?><div class="error"><p>Estos cambios no tendran efecto ya que no se usa este css</p></div><?php endif; ?>
	<?php if (isset($errors)):?>
		<ul class="error">
			<?php foreach ($errors as $message): ?>
				<li><?php echo $message ?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>	
	<p><?php echo $form->label('width', 'Width: '); ?><?php echo $form->input('width', $post['width'])?>px</p>
	<p><?php echo $form->label('height', 'Height: '); ?><?php echo $form->input('height', $post['height'])?>px</p>
	<?php if ($backend->get_use('css') == 0):?><p><a href="#">Download Css</a></p><?php endif;?>
	<div class="submit"><input type="submit" value="Update" name="action" /></div>
<?php echo $form->close(); ?>