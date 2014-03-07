<script>
	var CATEGORY_ID = '<?php echo (int) $category->id; ?>';
</script>

<div id="Photos" class="widget">
	<div class="widget-header">
		<?php if( ! Request::initial()->is_iframe()): ?>
		<?php echo UI::button(__('Add category'), array(
			'icon' => UI::icon( 'plus' ), 'id' => 'create-category', 'data-target' => '#category-modal'
		)); ?>
		
		<?php if($category->loaded()): ?>
		<?php echo UI::button(__('Edit category'), array(
			'icon' => UI::icon( 'cog' ), 
			'id' => 'edit-category',
			'data-target' => '#category-modal', 
			'class' => 'btn btn-primary'
		)); ?>
		<?php endif; ?>
		
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
	
	<div class="widget-content">
		<?php if(!empty($categories)): ?>
		<div class="thumbnails categories droppable pull-left">
			<?php foreach ($categories as $cat): ?>
				<?php echo View::factory('photos/category', array('category' => $cat)); ?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	
		<div class="thumbnails sortable photos">
			<?php if(!empty($photos)): ?>
			<?php foreach ($photos as $photo): ?>
				<?php echo View::factory('photos/image', array('photo' => $photo, 'category' => $category)); ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<br />
<div class="widget">
	<div class="widget-header">
		<?php echo UI::icon('upload'); ?><h3><?php echo __('Upload photos'); ?></h3>
	</div>
	<div class="widget-content">
	<?php 
		echo Form::open('/api-photos.upload', array(
			'enctype' => 'multipart/form-data',
			'method' => Request::POST,
			'class' => 'dropzone',
		));
		echo Form::hidden('token', Security::token()); 
		echo Form::close(); 
	?>
	</div>
</div>

<br />
<div class="widget" id="upload-video">
	<div class="widget-header">
		<?php echo UI::icon('upload'); ?><h3><?php echo __('Upload by url'); ?></h3>
	</div>

	<?php 
	echo Form::open('/api-photos.from_url', array(
		'method' => Request::POST, 'class' => 'form-horizontal'
	));
	echo Form::hidden('token', Security::token()); 
	echo Form::hidden('category_id', $category->id); 
	?>
	<div class="widget-content">
		<div class="control-group">
			<label class="control-label" for="file-url"><?php echo __('Url to file'); ?></label>
			<div class="controls">
				<?php echo Form::input('url', NULL, array('class' => 'input-block-level')); ?>
			</div>
		</div>
	</div>
	<div class="widget-footer">
		<button class="btn btn-primary pull-right"><?php echo __('Upload'); ?></button>
		<div class="clearfix"></div>
	</div>
	<?php echo Form::close(); ?>
</div>

<?php echo View::factory('photos/blocks/category_form', array(
	'category' => ORM::factory('Photo_Category')
)); ?>