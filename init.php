<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Plugin::factory('gallery', array(
	'title' => __('Gallery'),
	'css' => 'gallery.css'
) )->register();