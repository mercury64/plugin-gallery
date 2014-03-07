<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	array(
		'name' => 'Content',
		'children' => array(
			array(
				'name' => __('Gallery'), 
				'url' => URL::backend('photos'),
				'icon' => 'picture',
				'priority' => 200,
				'permissions' => 'photos.index'
			)
		)
	)
);