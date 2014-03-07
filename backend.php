<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set( 'view_category', ADMIN_DIR_NAME.'/photos/category(/<id>)', array(
	'id' => '[0-9]+'
) )
	->defaults( array(
		'controller' => 'photos',
		'action' => 'index'
	) );

Assets::css('gallery', ADMIN_RESOURCES . 'css/gallery.css');