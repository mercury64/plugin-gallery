<div id="category-modal" class="modal hide fade" tabindex="-1"  aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3><?php echo __('New category'); ?></h3>
	</div>
	<form class="form-horizontal" action="#" method="post">
		<?php echo Form::hidden('id', $category->id); ?>
		<div class="modal-body">
			<div class="control-group">
				<label class="control-label" for="category-title"><?php echo __('Category title'); ?></label>
				<div class="controls">
					<input type="text" name="title" id="category-title" class="slug-generator" data-separator="_" value="<?php echo $category->title; ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="category-description"><?php echo __('Category description'); ?></label>
				<div class="controls">
					<textarea name="description" id="category-description"><?php echo $category->description; ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="slug-name"><?php echo __('Category slug'); ?></label>
				<div class="controls">
					<input type="text" name="slug" id="slug-name" class="slug" value="<?php echo $category->slug; ?>" />
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('Cancel'); ?></a>
			<button class="create-category-btn btn btn-primary"><?php echo __('Save'); ?></button>
		</div>
	</form>
</div>
