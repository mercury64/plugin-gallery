<div class="widget-content">
	<div class="control-group">
		<label class="control-label" for="id_ctx"><?php echo __('Category ID (Ctx)'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'id_ctx', $widget->id_ctx, array(
				'class' => 'input-small', 'id' => 'id_ctx'
			) ); ?>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label" for="path_ctx"><?php echo __('Category path (Ctx)'); ?></label>
		<div class="controls">
			<?php echo Form::input( 'path_ctx', $widget->path_ctx, array(
				'class' => 'input-small', 'id' => 'path_ctx'
			) ); ?>
		</div>
	</div>
	
	<hr />
	<?php echo Bootstrap_Form_Element_Control_Group::factory(array(
		'element' => Bootstrap_Form_Element_Select::factory(array(
			'name' => 'category_id', 'options' => $categories
		))
		->attributes('class', Bootstrap_Form_Element_Input::XXLARGE)
		->selected($widget->category_id)
		->label(__('Root category'))
	)); ?>
	<div class="control-group">
		<div class="controls">
			<label class="checkbox"><?php echo Form::checkbox('with_images', 1, $widget->with_images); ?> <?php echo __('Show only categories with attached image'); ?></label>
		</div>
	</div>
</div>