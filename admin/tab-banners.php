<?php
include_once dirname(dirname(__FILE__)) . '/class/bannersImages.php';
$backend = new bannersImages($page);
$form = new wpfw_form (get_bloginfo('charset'));
$action = 'new';
$post = array(
	'desc' => '',
	'link' => '',
	'show' => '1',
	'order' => ''
);

//print_r($_POST);

if (array_key_exists('action', $_POST)){
	$action = key($_POST['action']);

	unset ($_POST['action']);

	switch ($action) {
		case 'add':			
			
			if ($backend->get_basic('overwrite') == 0){
				$_FILES['uploadFile']['name'] = $backend->rename_image($_FILES['uploadFile']['name']);
			}
			
			$post = new validation($_POST);
			$upload = new uploading($_FILES);
			$upload->set_overwrite($backend->get_basic('overwrite'));			
			
			$post->rule('desc', 'alpha_numeric');
			$post->rule('link', 'alpha_dash');
			if (array_key_exists('order', $post)) {
				$post->rule('order', 'digit');
			}
			$post->rule('show', 'digit');
			
			$upload->rule('not_emtpy');
			
			if ($post->check() && $upload->check() && $upload->save($backend->get_upload_banner_dir(). 'original')){
				$file = $upload->get_file();
				
				$source = $backend->get_upload_banner_dir(). 'original/' . $file['name'];
				$destination = $backend->get_upload_banner_dir(). '/' . $file['name'];
				
				copy($source, $destination);

				//rezise
				if ($backend->get_basic('resize') == 1){
					
					$image = new image_GD($destination);

					if ($backend->get_basic('crop') == 1){
						//crop
						$image->crop($backend->get_size('w'), $backend->get_size('h'));						
					}else {
						//rezise
						$image->resize($backend->get_size('w'), $backend->get_size('h'), image_GD::NONE);	
					}
					$image->save();
				}
						
				//add to db				
				$backend->set_alt($post['desc']);
				$backend->set_image($file['name']);
				$backend->set_link($post['link']);
				$backend->set_show($post['show']);
				if (array_key_exists('order', $post)) {
					$backend->set_order($post['order']);	
				}
				$backend->insert();
				
				$backend->make_html();
				
				$post = array(
					'desc' => '',
					'link' => '',
					'show' => '1',
					'order' => ''
				);				
			} else {
				 $errors = $post->errors();
			}
			break;
			
		case 'show':
			$image = $backend->get_image($_POST['id']);
			$post['id'] = $image['id'];
			$post['desc'] = $image['alt'];
			$post['link'] = $image['link'];
			$post['show'] = $image['show'];
			$post['order'] = $image['order'];
			$post['image'] = $image['image'];
			unset($image);
			break;
		case 'edit':		
			
			$delete_img = false;
			
			if ($_FILES['uploadFile']['name'] != '') {
				
				$image = $backend->get_image($_POST['id']);
				$_FILES['uploadFile']['name'] = $backend->sanitize_filename($_FILES['uploadFile']['name']);
				
				if ($image['image'] != $_FILES['uploadFile']['name']){
					$delete_img = true;
					$_FILES['uploadFile']['name'] = $backend->rename_image($_FILES['uploadFile']['name']);
				}
				
				unset($image);
				
				$upload = new uploading($_FILES);
				$upload->set_overwrite($backend->get_basic('overwrite'));
				$upload->rule('not_emtpy');				
			}else {
				$upload = false;
			}
			
			$post = new validation($_POST);
			$post->rule('id', 'digit');
			$post->rule('desc', 'alpha_numeric');
			$post->rule('link', 'alpha_dash');
			if (array_key_exists('order', $post)) {
				$post->rule('order', 'digit');
			}
			$post->rule('show', 'digit');				
			
			$is_ok = false;
			
			if ($upload != false){
				if ($post->check() && $upload->check() && $upload->save($backend->get_upload_banner_dir(). 'original')){
					$is_ok = true;
				}				
			}else{
				if ($post->check()){
					$is_ok = true;
				}
			}
			
			if ($is_ok == true){
				if ($upload != false ){
					//subio img y debo borrar la anterior y poner la nueva
					if ($delete_img == true) {
						$backend->delete_image($post['id']);
					}

					$file = $upload->get_file();
					
					$source = $backend->get_upload_banner_dir(). 'original/' . $file['name'];
					$destination = $backend->get_upload_banner_dir(). '/' . $file['name'];
					
					copy($source, $destination);	

					//rezise
					if ($backend->get_basic('resize') == 1){
						
						$image = new image_GD($destination);
	
						if ($backend->get_basic('crop') == 1){
							//crop
							$image->crop($backend->get_size('w'), $backend->get_size('h'));						
						}else {
							//rezise
							$image->resize($backend->get_size('w'), $backend->get_size('h'), image_GD::NONE);	
						}
						$image->save();
					}
					
					$backend->set_image($file['name']);			
					echo 'cambie img';					
				}

				//update to db	
				$backend->set_id($post['id']);
				$backend->set_alt($post['desc']);				
				$backend->set_link($post['link']);
				$backend->set_show($post['show']);
				if (array_key_exists('order', $post)) {
					$backend->set_order($post['order']);	
				}
				$backend->update();
				
				$backend->make_html();
				
				$post = array(
					'desc' => '',
					'link' => '',
					'show' => '1',
					'order' => ''
				);						
			}else {
				$errors = $post->errors();
				$action = 'show';
				$image = $backend->get_image($_POST['id']);
				$post['image'] = $image['image'];
				unset($image);
			}
			break;
		case 'delete':
			$backend->delete($_POST['id']);
			$backend->make_html();
			break;
	}
}
//switch

?>
<?php echo $form->open('', array('enctype' => 'multipart/form-data')); ?>
	<h2><?php if ($action == 'show'):?>Edit image<?php else: ?>Add images<?php endif; ?></h2>
	<?php if (isset($errors)):?>
		<ul class="error">
			<?php foreach ($errors as $message): ?>
				<li><?php echo $message ?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>	
	<?php if ($action == 'show'): //when must show an image to edit?>
		<?php echo $form->hidden('id', $post['id'])?>
	<?php else: ?>
		<?php echo $form->hidden('action', 'adding')?>
	<?php endif;?>
	<p><?php echo $form->label('desc', 'Description: ')?><?php echo $form->input('desc', $post['desc']); ?></p>
	<p><?php echo $form->label('link', 'Link: ')?><?php echo $form->input('link', $post['link']); ?></p>
	<p><?php echo $form->label('show', 'Show: ')?><?php echo $form->checkbox('show', $post['show'], $post['show'] == 1 ? true: false); ?></p>
	<noscript><p><?php echo $form->label('order', 'Order: ')?><?php echo $form->input('order', $post['order']); ?></p></noscript>
	<?php if (array_key_exists('image', $post)): ?><p class="image"><img height="200" src="<?php echo $backend->get_upload_banner_url() . '/' . $post['image'];?>" /></p><?php endif;?>
	<p><?php echo $form->label('uploadFile', 'Image: ')?><input type="file" id="uploadFile" name="uploadFile" value="" /></p>
	<div class="submit">
		<?php
			if ($action == 'show'){
				echo $form->submit('action[edit]', 'Edit Image');
			} else {
				echo $form->submit('action[add]', 'Add Image');
			}
		?>
	</div>
<?php $form->close();?>
<?php if ($action != 'show'):?>
<div>
	<?php $images = $backend->get_images(); ?>
	<?php if(count($images) != 0): ?>
		<h2>Manage images</h2>
		<table>
			<thead>
				<td>Image</td>
				<td>Info</td>
				<td>Show?</td>
				<td>Actions</td>
			</thead>
			<?php foreach($images as $image):?>
			<tr>
				<td><img height="100" src="<?php echo $backend->get_upload_banner_url() . $image->image; ?>" /></td>
				<td>Link: <?php echo $image->link; ?>
				<br />Description: <?php echo $image->alt; ?></td>
				<td><?php if ($image->show == 1):?>Yes<?php else: ?>No<?php endif;?></td>
				<td>
					<?php echo $form->open(); ?>
						<?php echo $form->hidden('id', $image->id)?>
						<div class="submit"><?php echo $form->submit('action[show]', 'Edit');?></div>
						<div class="submit"><?php echo $form->submit('action[delete]', 'Delete');?></div>
					<?php echo $form->close(); ?>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
	<?php else: ?>
		<h2>Not images yet</h2>
	<?php endif;?>
</div>
<?php endif; ?>