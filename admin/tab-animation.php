<?php
include_once dirname(dirname(__FILE__)) . '/class/bannersAnimation.php'; 
$backend = new bannersAnimation($page);
$form = new wpfw_form(get_bloginfo('charset'));

$post = array(
	'timeout' => $backend->get_custom_js('timeout'),
	'speed' => $backend->get_custom_js('speed'),
	'pause' => $backend->get_custom_js('pause'),
	'pager' => $backend->get_custom_js('pager'),
	'pagerAnchorBuilder' => $backend->get_custom_js('pagerAnchorBuilder')
);

if (array_key_exists('action', $_POST)) {

	unset($_POST['action']);
	
	$post = new validation($_POST);
	
	$post->rule('timeout', 'not_empty');
	$post->rule('timeout', 'digit');
	$post->rule('speed', 'not_empty');
	$post->rule('speed', 'digit');
	$post->rule('pause', 'not_empty');
	$post->rule('pager', 'not_empty');
	$post->rule('pagerAnchorBuilder', 'not_empty');
	
	if ($post->check()){

		$backend->set_custom_js('timeout', empty($post['timeout']) ? 4000 : $post['timeout']);
		$backend->set_custom_js('speed', empty($post['speed']) ? 1000 : $post['speed']);
		$backend->set_custom_js('pause', $post['pause'] == 1 ? 1 : 0);
		$backend->set_custom_js('pager', $post['pager'] == 1 ? 1 : 0);
		$backend->set_custom_js('pagerAnchorBuilder', $post['pagerAnchorBuilder']);
		$backend->update_custom_js();
		
		$backend->set_basic('cycle', $backend->get_custom_js('pager') == 1 ? 'cycle' : 'cycle.lite');
		$backend->update_basic();
		
		$backend->make_js();
			
	} else {
		$errors = $post->errors();
	}

}

?>
<?php echo $form->open(); ?>
	<h2>Animation</h2>
	<?php if ($backend->get_use('js') == 0):?><p class="error">You have chosse don't use the plugin's javascript, this change won't afect the function</p><?php endif; ?>
	<?php if (isset($errors)):?>
		<ul class="error">
			<?php foreach ($errors as $message): ?>
				<li><?php echo $message ?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>
	<p><?php echo $form->label('timeout', 'Timeout'); ?><?php echo $form->input('timeout', $post['timeout'])?><span class="desc">Milliseconds between slide transitions (0 to disable auto advance)</span></p>
	<p><?php echo $form->label('speed', 'Speed'); ?><?php echo $form->input('speed', $post['speed'])?><span class="desc">Speed of the transition</span></p>
	<p>Pause on Hover?
		<?php echo $form->label('pause_yes', $form->radio('pause', 1, $post['pause'] == 1 ? true: false, array('id' => 'pause_yes')) . ' Yes');?>
		<?php echo $form->label('pause_no', $form->radio('pause', 0, $post['pause'] == 0 ? true: false, array('id' => 'pause_no')) . ' No');?>
	</p>
	<p>Use a pager?
		<?php echo $form->label('pager_yes', $form->radio('pager', 1, $post['pager'] == 1 ? true: false, array('id' => 'pager_yes')) . ' Yes');?>
		<?php echo $form->label('pager_no', $form->radio('pager', 0, $post['pager'] == 0 ? true: false, array('id' => 'pager_no')) . ' No');?>
	</p>	
	<p><?php echo $form->label('pagerAnchorBuilder', 'Pager Type:'); ?>
		<?php $pager_type = array('blank' => 'Blank', 'numbers' => 'Numbers', 'letters' => 'Letters')?>
		<?php echo $form->select('pagerAnchorBuilder',$pager_type, $post['pagerAnchorBuilder'])?>
		<span class="desc">pager_type</span>
	</p>
	<?php if ($backend->get_use('js') == 0):?>
		<?php $file = $backend->get_plugin_dir() . '/plugin/custom.js'; ?>
		<?php if(file_exists($file)) :?>
			<h3>Use this Javascript</h3>
			<textarea class="large-text readonly" readonly="readonly" rows="5" ><?php include_once($file); ?></textarea>
		<?php endif; ?>
	<?php endif; ?>
	<div class="submit"><?php echo $form->submit('action', 'Update')?></div>
<?php echo $form->close(); ?>