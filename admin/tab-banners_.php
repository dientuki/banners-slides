<?php
$backend = new bannersBackend($page);


global $wpdb;
$table = $wpdb->get_blog_prefix() . AQUIT_BANNERS_TABLE;

$action = strtolower($_POST['action']);
//unset($_POST['action']); 
$error = false;
$sucefull = false;
$image = array();

/**
* Customized version of wp_handle_upload from v2.5.1 wp-admin/includes/file.php
* 
* @param array $config La configuracion que pusieron en el post
* @return void
*/
function aquit_upload_dir(){
	global $switched;
	$siteurl = get_option( 'siteurl' );
	$upload_path = get_option( 'upload_path' );
	$upload_path = trim($upload_path);
	$main_override = defined( 'MULTISITE' ) && is_main_site();
	if ( empty($upload_path) ) {
		$dir = WP_CONTENT_DIR . '/uploads';
	} else {
		$dir = $upload_path;
		if ( 'wp-content/uploads' == $upload_path ) {
			$dir = WP_CONTENT_DIR . '/uploads';
		} elseif ( 0 !== strpos($dir, ABSPATH) ) {
			// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
			$dir = path_join( ABSPATH, $dir );
		}
	}

	if ( !$url = get_option( 'upload_url_path' ) ) {
		if ( empty($upload_path) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) )
			$url = WP_CONTENT_URL . '/uploads';
		else
			$url = trailingslashit( $siteurl ) . $upload_path;
	}

	if ( defined('UPLOADS') && !$main_override && ( !isset( $switched ) || $switched === false ) ) {
		$dir = ABSPATH . UPLOADS;
		$url = trailingslashit( $siteurl ) . UPLOADS;
	}

	if ( is_multisite() && !$main_override && ( !isset( $switched ) || $switched === false ) ) {
		if ( defined( 'BLOGUPLOADDIR' ) )
			$dir = untrailingslashit(BLOGUPLOADDIR);
		$url = str_replace( UPLOADS, 'files', $url );
	}

	$bdir = $dir;
	$burl = $url;

	$subdir = BANNER_URL;

	$dir .= $subdir;
	$url .= $subdir;

	$uploads = apply_filters( 'upload_dir', array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir, 'basedir' => $bdir, 'baseurl' => $burl, 'error' => false ) );

	// Make sure we have an uploads dir
	if ( ! wp_mkdir_p( $uploads['path'] ) ) {
		$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $uploads['path'] );
		return array( 'error' => $message );
	}
	
	return $uploads;
	
}

/**
* Customized version of wp_handle_upload from v2.5.1 wp-admin/includes/file.php
* 
* @param array $config La configuracion que pusieron en el post
* @param array $config La configuracion que pusieron en el post
* @param array $config La configuracion que pusieron en el post 
* @return void
*/
function handle_upload( &$file, $overwriteFile, $renameIfExists ) {
	// The default error handler.
	if (! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}
	
	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';
	
	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_upload';
	
	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		'The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.',
		'The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form.',
		'The uploaded file was only partially uploaded.',
		'No file was uploaded.',
		'Missing a temporary folder.',
		'Failed to write file to disk.');
	
	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;
	$mimes = false;
	
	// Customizable overrides
	$uploads = aquit_upload_dir();
	
	$message = '';
	
	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
		return $upload_error_handler( $file, $upload_error_strings[$file['error']] );
	
	// A non-empty file will pass this test.
	if ( !($file['size'] > 0 ) )
		return $upload_error_handler( $file, 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.' );
	
	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
		return $upload_error_handler( $file, 'Specified file failed upload test.' );
	
	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype( $file['name'], $mimes );
		
		extract( $wp_filetype );
		
		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return $upload_error_handler( $file, 'File type does not meet security guidelines. Try another.' );
		
		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');
		
		if ( !$type )
			$type = $file['type'];
	}
	
	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( false !== $uploads['error'] )
		return $upload_error_handler( $file, $uploads['error'] );
	
	$uploads['path'] = untrailingslashit( $uploads['path'] );
	$uploads['path'] = preg_replace( '/\/+/', '/', $uploads['path'] );
	$uploads['url'] = untrailingslashit( $uploads['url'] );
	
	$filename = $file['name'];
	
	/*
	if ( file_exists( $uploads['path'] . '/' . $file['name'] ) ) {
		if ( $overwriteFile )
			$filename = $file['name'];
		elseif ( ! $renameIfExists )
			return $upload_error_handler( $file, 'The file already exists. Since overwriting and renaming are not permitted, the file was not added.' );
	}
	*/
	
	if ( false === @move_uploaded_file( $file['tmp_name'], $uploads['path'] . '/' . $filename ) ) {
		/*
		if ( $overwriteFile ) {
			if ( false === @move_uploaded_file( $file['tmp_name'], $uploads['path'] . '/' . $filename ) ) {
				//return $upload_error_handler( $file, sprintf( 'The uploaded file could not be moved to %s. Please check the folder and file permissions.' ), $uploads['path'] ) );
			} else {
				$message = 'Unable to overwrite existing file. Since renaming is permitted, the file was saved with a new name.';
			}
		}
		else {
		*/
			return $upload_error_handler( $file, sprintf( 'The uploaded file could not be moved to %s. Please check the folder and file permissions.', $uploads['path'] ) );
		/*}*/
	}

	$stat = stat( dirname( $uploads['path'] . '/' . $filename ) );
	$perms = $stat['mode'] & 0000666;
	@chmod( $uploads['path'] . '/' . $filename, $perms );
	
	// Compute the URL
	//$url = $uploads['url'] . '/' . $filename;
	
	$return = apply_filters( 'wp_handle_upload', array( 'name' => $filename, 'message' => $message, 'error' => false ) );
	
	return $return;
}

/**
* Get File from Post
* 
* @param array $file the $_FILE to manage
* @param boolean $overwriteFile
* @param boolean $renameIfExists L
* @return array $file
*/
function get_file( $file, $overwriteFile = true, $renameIfExists = false ) {
	$file['name'] = sanitize_file_name($file['name']);
	
	$results = handle_upload($file, $overwriteFile);
	
	if ( empty( $results['error'] ) ) {
		$file['name'] = $results['name'];
		$file['message'] = $results['message'];
		$file['error'] = false;
	}
	else {
		$file['error'] = $results['error'];
	}
	
	return $file;
}

/**
* Remove File
* 
* @param string $value filename
* @return void
*/
function remove_image($value){
	$uploads = aquit_upload_dir();
	$value = $uploads['path'] . '/' . $value;
	
	if (file_exists($value)) {
		chmod($value, 0777);
		unlink($value);
	}	
}

switch ($action){
	case 'edit': //when must show an image to edit
		$sql = 'SELECT id, `order`, image, alt, link, `show` FROM ' . $table . ' WHERE id = ' . $_POST['id'];
		$image = $wpdb->get_row($sql, ARRAY_A);
		break;
		
	case 'adding': //when is adding an image

		//uploading a file
		if ($_FILES['uploadFile']['name']){
			$file = get_file( $_FILES['uploadFile']);
		} else {
			$error = 'You must put a file';
		}

		//adding everthing to the db
		if ($file['error'] == false){

			$image = $file['name'];
			$alt = $_POST['desc'];
			$link = $_POST['link'];
			$order = isset($_POST['order']) == true ? $_POST['order'] : 0;
			$show = isset($_POST['show']) == true ? '1': '0';
			
			//INSERT INTO `aquit_develop`.`wp_aquit_banners` (`id`, `image`, `alt`, `link`, `show`) VALUES (NULL, 'image', 'alt', 'link', '0');
			//upload everthing to db
			$sql = 'INSERT INTO ' . $table;
			$sql .= ' (id, `order`, image, alt, link, `show`) VALUES';
			$sql .= ' (NULL, \''. $order . '\', \'' . $image . '\', \''. $alt . '\', \'' . $link . '\', \'' . $show . '\')';

			$wpdb->query($sql);
			
			//@todo: redirect con ?sucefull=true
			
		} else {
			$error = $file['error'];
		}
		
		break;
		
	case 'editing': //when is editing an image
		
		$image = false;
		
		if ($_FILES['uploadFile']['name']){
			$file = get_file( $_FILES['uploadFile']);
			$image_new = $file['name'];
			
			$sql = 'SELECT image FROM ' . $table . ' WHERE id = ' . $_POST['id'];
			$image_old = $wpdb->get_row($sql, ARRAY_A);
			$image_old = $image_old['image'];
			if($image_old != $image_new){
				remove_image($image_old);
				$image = $image_new;
			}
		}
		
		$alt = $_POST['desc'];
		$link = $_POST['link'];
		$order = isset($_POST['order']) == true ? $_POST['order'] : 0;
		$show = isset($_POST['show']) == true ? '1': '0';
		
		//update everthing to db
		$sql = 'UPDATE ' . $table . ' SET ';
		$sql .= ' `order` = \''. $order . '\', alt = \''. $alt . '\', link = \'' . $link . '\', `show` = \'' . $show . '\'';
		if ($image != false){
			$sql .= ', image = \'' . $image . '\'';
		}
		$sql .= ' WHERE id = ' . $_POST['id'];
		
		$wpdb->query($sql);

		unset($image);
		
		break;
		
	case 'delete': //when is deleting an imagen
		
		//delete image
		$sql = 'SELECT image FROM ' . $table . ' WHERE id = ' . $_POST['id'];
		$image = $wpdb->get_row($sql, ARRAY_A);
		remove_image($image['image']);
		
		//delete row
		$sql = 'DELETE FROM ' . $table . ' WHERE id = ' . $_POST['id'] . ' LIMIT 1';
		$wpdb->query($sql);
		
		break;
}

if (($error == false) && (($action == 'adding') || ($action == 'editing') || ($action == 'delete'))){
	make_html();
}

?>
<form method='post' action="<?php echo $_SERVER["REQUEST_URI"]; ?>" enctype="multipart/form-data">
	<h2>Add images</h2>
	<?php if ($error != false): ?>
		<p class="error"><?php echo $error; ?></p>
	<?php endif; ?>
	<?php if ( ($error == false) && (($action == 'adding') || ($action == 'editing') || ($action == 'delete')) ): ?>
		<div id="message" class="updated fade"><p>Wiiii, action do it with out problem pal</p></div>
	<?php endif;?>
	<?php if ($action == 'edit'): //when must show an image to edit?>
		<input type="hidden" name="action" value="editing">
		<input type="hidden" name="id" value="<?php echo $image['id']; ?>">
	<?php else: ?>
		<input type="hidden" name="action" value="adding">
	<?php endif;?>
	<p><label for="desc">Description: </label><input type="text" id="desc" name="desc" value="<?php echo $image['alt']; ?>" /></p>
	<p><label for="link">Link: </label><input type="text" id="link" name="link" value="<?php echo $image['link']; ?>" /></p>
	<p><label for="show">Show: </label><input type="checkbox" id="show" name="show" value="<?php echo isset($image['show']) ? $image['show']: 1;?>" <?php if($image['show'] == 1): ?>checked="checked"<?php endif;?> /></p>
	<noscript><p><label for="order">Orden: </label><input type="text" id="order" name="order" value="<?php echo $image['order'];?>" /></p></noscript>
	<?php if ($image['image']): ?><p class="image"><img height="200" src="<?php echo BANNER_FULL_URL . $image['image']; ?>" /></p><?php endif;?>
	<p><label for="uploadFile">Image: </label><input type="file" id="uploadFile" name="uploadFile" value="" /></p>
	<div class="submit"><?php if ($action == 'edit'): ?><input type="submit" value="Edit Image" name="action[edit]" /><?php else: ?><input type="submit" value="Add Image" name="action[add]" /><?php endif;?></div>
</form>

<div>
	<?php $images = $backend->get_images(); ?>
	<?php if(count($images) != 0): ?>
		<h2>Edit images</h2>
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
					<form method='post' action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
						<input type="hidden" name="id" value="<?php echo $image->id?>">
						<div class="submit"><input type="submit" value="Edit" name="action[show]"></div>
						<div class="submit"><input type="submit" value="Delete" name="action[delete]"></div>
					</form>
				</td>
			</tr>
			<?php endforeach;?>
		</table>
	<?php else: ?>
		<h2>Not images yet</h2>
	<?php endif;?>
</div>