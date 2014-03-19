<div class="control-group">
	<label class="control-label"><?php echo $field->header; ?> <?php if($field->isreq): ?>*<?php endif; ?></label>
	<div class="controls">
		<?php echo Form::select($field->name, $field->get_gallery_categories(), $value); ?>
		
		<?php if(!empty($value) AND $field->category_exists($value)): ?>
		<?php echo UI::button(__('View'), array(
			'href' => Route::url('backend', array(
				'controller' => 'photos',
				'action' => 'category',
				'id' => $value
			)),
			'icon' => UI::icon('building'),
			'class' => 'btn popup fancybox.iframe'
		)); ?>
		<?php else: ?>
		
		<?php endif; ?>
	</div>
</div>