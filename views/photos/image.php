<div class="span1" data-id="<?php echo $photo->id; ?>">
	<?php if($category->id > 0 AND $category->image != $photo->filename AND $photo->is_image()): ?>
	<?php echo UI::icon('picture option'); ?>
	<?php endif; ?>
	<?php echo UI::icon('trash option'); ?>
	<div class="thumbnail <?php if($category->image == $photo->filename): ?>category-image<?php endif; ?>">
		<?php if($photo->is_image()): ?>
		<?php echo HTML::anchor($photo->full(), HTML::image($photo->thumb()), array('class' => 'fancybox-image', 'rel' => 'gallery')); ?>
		<?php else: ?>
		<?php echo HTML::anchor($photo->filename, __('Video'), array('class' => 'fancybox-image fancybox.iframe', 'rel' => 'gallery')); ?>
		<?php endif; ?>
	</div>
</div>